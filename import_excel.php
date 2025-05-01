<?php
// Prevent PHP from outputting errors directly to the browser
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Start output buffering to catch unexpected output
ob_start();

// Set content type to JSON
header('Content-Type: application/json');

// Register shutdown function to handle fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        // Clear any output that might have been generated
        ob_end_clean();
        
        // Log the error
        file_put_contents(__DIR__ . '/excel_debug.log', "Fatal Error: {$error['message']} in {$error['file']} on line {$error['line']}\n", FILE_APPEND);
        
        // Return a JSON error response
        echo json_encode([
            'success' => false,
            'message' => "A fatal error occurred: {$error['message']}"
        ]);
    }
});

// Debug mode
define('DEBUG_MODE', true);

// Clear the debug log file to start fresh
file_put_contents(__DIR__ . '/excel_debug.log', "Starting Excel Import...\n");

// Database connection parameters
$host = 'localhost';
$dbname = 'famsattendance';
$username = 'root';
$password = '';

// Check if a file was uploaded
if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode([
        'success' => false,
        'message' => "Please upload a valid Excel file."
    ]);
    exit;
}

// Only accept CSV files for simplicity
$fileExtension = strtolower(pathinfo($_FILES['excel_file']['name'], PATHINFO_EXTENSION));
if ($fileExtension !== 'csv') {
    echo json_encode([
        'success' => false,
        'message' => "Please upload a CSV file. Save your Excel file as CSV first."
    ]);
    exit;
}

/**
 * Format time value to proper HH:MM:SS format
 * Handles multiple time formats (Excel numeric, HH:MM, etc.)
 */
function formatTime($time) {
    if (empty($time)) return null;
    
    // If already in correct format, return as is
    if (preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $time)) {
        return strlen($time) == 5 ? "$time:00" : $time;
    }
    
    // Try to convert numeric time (Excel fraction of day)
    if (is_numeric($time)) {
        $seconds = round($time * 86400);
        $hours = floor($seconds / 3600);
        $seconds %= 3600;
        $minutes = floor($seconds / 60);
        $seconds %= 60;
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
    
    // Try with strtotime as last resort
    $timestamp = strtotime($time);
    if ($timestamp !== false) {
        return date('H:i:s', $timestamp);
    }
    
    return null;
}

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Log database connection
    file_put_contents(__DIR__ . '/excel_debug.log', "Database connected successfully\n", FILE_APPEND);
    
    // Load the CSV file
    $inputFileName = $_FILES['excel_file']['tmp_name'];
    file_put_contents(__DIR__ . '/excel_debug.log', "Loading CSV file: $inputFileName\n", FILE_APPEND);
    
    // Open the CSV file
    $file = fopen($inputFileName, 'r');
    if (!$file) {
        throw new Exception("Could not open the CSV file.");
    }
    
    // Skip header row
    fgetcsv($file);
    
    // Begin transaction
    $pdo->beginTransaction();
    
    // Two arrays to track data
    $employeeData = [];
    $attendanceData = [];
    $rowCount = 0;
    
    // Read all rows from CSV
    while (($row = fgetcsv($file)) !== FALSE) {
        $rowCount++;
        
        // Check if we have enough columns
        if (count($row) < 8) {
            file_put_contents(__DIR__ . '/excel_debug.log', "Warning: Row $rowCount has less than 8 columns. Skipping.\n", FILE_APPEND);
            continue;
        }
        
        // Read REAL data from CSV - direct mapping from Excel columns
        $empId = trim($row[0]);
        $name = trim($row[1]);
        $dept = trim($row[2]);
        $date = trim($row[3]);
        $amIn = trim($row[4]);
        $amOut = trim($row[5]);
        $pmIn = trim($row[6]);
        $pmOut = trim($row[7]);
        
        // Skip header rows
        if ($empId == 'ID' || $name == 'Name' || $dept == 'Department' || 
            strpos($empId, 'Stat.Date') !== false) {
            file_put_contents(__DIR__ . '/excel_debug.log', "Skipping header row: $empId, $name\n", FILE_APPEND);
            continue;
        }
        
        // Format date properly
        if (!empty($date)) {
            // Handle m/d/Y format (4/30/2025)
            if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $date, $matches)) {
                $month = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                $day = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
                $year = $matches[3];
                $date = "$year-$month-$day";
            } else if (is_numeric($date)) {
                // Handle Excel numeric date
                $excelBaseDate = new DateTime('1900-01-01');
                $excelBaseDate->modify('+' . ($date - 2) . ' days');
                $date = $excelBaseDate->format('Y-m-d');
            } else if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                // Try strtotime as last resort
                $date = date('Y-m-d', strtotime($date));
            }
        }
        
        // Format time values
        $amIn = formatTime($amIn);
        $amOut = formatTime($amOut);
        $pmIn = formatTime($pmIn);
        $pmOut = formatTime($pmOut);
        
        // Store employee data (update if ID exists)
        if (!empty($empId) && !empty($name)) {
            $employeeData[$empId] = [
                'name' => $name,
                'dept' => $dept
            ];
        }
        
        // Store attendance record - use emp_id + date as key to handle duplicates in file
        if (!empty($empId) && !empty($date)) {
            // Using empId_date as key means we only keep the last occurrence
            // of each empId+date combination from the file, as per requirements
            $key = $empId . '_' . $date;
            $attendanceData[$key] = [
                'emp_id' => $empId,
                'name' => $name,
                'dept' => $dept,
                'date' => $date,
                'am_in' => $amIn,
                'am_out' => $amOut,
                'pm_in' => $pmIn,
                'pm_out' => $pmOut,
                'row' => $rowCount
            ];
        }
    }
    
    // Close the file
    fclose($file);
    
    file_put_contents(__DIR__ . '/excel_debug.log', "Found " . count($employeeData) . " unique employees and " . count($attendanceData) . " attendance records.\n", FILE_APPEND);
    
    // STEP 1: Process employees first - USING ONLY REAL DATA FROM CSV
    $employeesInserted = 0;
    $employeesUpdated = 0;
    
    foreach ($employeeData as $id => $data) {
        // Skip if no ID or name
        if (empty($id) || empty($data['name'])) continue;
        
        // Check if employee exists
        $stmt = $pdo->prepare("SELECT ID FROM emp_info WHERE ID = ?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() > 0) {
            // Update existing employee with REAL Excel data
            $stmt = $pdo->prepare("UPDATE emp_info SET NAME = ?, DEPT = ? WHERE ID = ?");
            $result = $stmt->execute([$data['name'], $data['dept'], $id]);
            if ($result) {
                $employeesUpdated++;
                file_put_contents(__DIR__ . '/excel_debug.log', "Updated employee: ID=$id, Name={$data['name']}\n", FILE_APPEND);
            }
        } else {
            // Insert new employee with REAL Excel data
            $stmt = $pdo->prepare("INSERT INTO emp_info (ID, NAME, DEPT, STATUS) VALUES (?, ?, ?, ?)");
            $result = $stmt->execute([$id, $data['name'], $data['dept'], '']);
            if ($result) {
                $employeesInserted++;
                file_put_contents(__DIR__ . '/excel_debug.log', "Inserted employee: ID=$id, Name={$data['name']}\n", FILE_APPEND);
            }
        }
    }
    
    // STEP 2: Process attendance records - USING ONLY REAL DATA FROM CSV
    $attendanceInserted = 0;
    $attendanceSkipped = 0;
    $attendanceUpdated = 0;
    
    foreach ($attendanceData as $key => $record) {
        $empId = $record['emp_id'];
        $date = $record['date'];
        $name = $record['name'];
        $amIn = $record['am_in'];
        $amOut = $record['am_out'];
        $pmIn = $record['pm_in'];
        $pmOut = $record['pm_out'];
        
        // Skip if missing critical data
        if (empty($empId) || empty($date)) {
            file_put_contents(__DIR__ . '/excel_debug.log', "Skipping attendance without ID or date: Name=$name, Date=$date\n", FILE_APPEND);
            continue;
        }
        
        // Check if this exact ID+DATE combination exists
        $stmt = $pdo->prepare("SELECT * FROM emp_rec WHERE EMP_ID = ? AND DATE = ?");
        $stmt->execute([$empId, $date]);
        
        if ($stmt->rowCount() > 0) {
            // This is an exact ID+DATE duplicate
            if (isset($_POST['update_existing']) && $_POST['update_existing'] == 'yes') {
                // Only update if user specifically requested updates
                $stmt = $pdo->prepare("UPDATE emp_rec SET AM_IN = ?, AM_OUT = ?, PM_IN = ?, PM_OUT = ? WHERE EMP_ID = ? AND DATE = ?");
                $stmt->execute([$amIn, $amOut, $pmIn, $pmOut, $empId, $date]);
                $attendanceUpdated++;
                file_put_contents(__DIR__ . '/excel_debug.log', "Updated attendance: ID=$empId, Date=$date\n", FILE_APPEND);
            } else {
                // Skip this record as user wanted to skip duplicates
                $attendanceSkipped++;
                file_put_contents(__DIR__ . '/excel_debug.log', "Skipping duplicate attendance: ID=$empId, Date=$date\n", FILE_APPEND);
            }
        } else {
            // No duplicate - insert new record with REAL Excel data
            $stmt = $pdo->prepare("INSERT INTO emp_rec (EMP_ID, DATE, AM_IN, AM_OUT, PM_IN, PM_OUT) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$empId, $date, $amIn, $amOut, $pmIn, $pmOut]);
            $attendanceInserted++;
            file_put_contents(__DIR__ . '/excel_debug.log', "Inserted attendance record: ID=$empId, Date=$date\n", FILE_APPEND);
        }
    }
    
    // Commit all changes
    $pdo->commit();
    
    // Success message
    $message = "Import successful: Added $employeesInserted new employees, updated $employeesUpdated existing employees, added $attendanceInserted new attendance records";
    if ($attendanceUpdated > 0) {
        $message .= ", updated $attendanceUpdated existing attendance records";
    }
    if ($attendanceSkipped > 0) {
        $message .= ", skipped $attendanceSkipped duplicate records";
    }
    $message .= ".";
    
    file_put_contents(__DIR__ . '/excel_debug.log', "$message\n", FILE_APPEND);
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => $message,
        'data' => [
            'employeesInserted' => $employeesInserted,
            'employeesUpdated' => $employeesUpdated,
            'attendanceInserted' => $attendanceInserted,
            'attendanceUpdated' => $attendanceUpdated,
            'attendanceSkipped' => $attendanceSkipped
        ]
    ]);
    
} catch (Exception $e) {
    // Rollback transaction if active
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Log error
    file_put_contents(__DIR__ . '/excel_debug.log', "ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
    
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => "Error: " . $e->getMessage()
    ]);
}
