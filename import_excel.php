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

// Only accept CSV or Excel files
$fileExtension = strtolower(pathinfo($_FILES['excel_file']['name'], PATHINFO_EXTENSION));
if (!in_array($fileExtension, ['csv', 'xls', 'xlsx'])) {
    echo json_encode([
        'success' => false,
        'message' => "Please upload a valid file. Supported formats: CSV, XLS, XLSX."
    ]);
    exit;
}

// Load the file
$inputFileName = $_FILES['excel_file']['tmp_name'];
$dataRows = [];

if ($fileExtension === 'csv') {
    // Handle CSV file
    $file = fopen($inputFileName, 'r');
    if (!$file) {
        throw new Exception("Could not open the CSV file.");
    }

    // Skip header row
    fgetcsv($file);

    // Read all rows from CSV
    while (($row = fgetcsv($file)) !== FALSE) {
        $dataRows[] = $row;
    }

    fclose($file);
} else {
    // Handle Excel file using PhpSpreadsheet
    require 'vendor/autoload.php'; // Ensure PhpSpreadsheet is loaded

    try {
        // Use the appropriate reader for .xls or .xlsx
        $reader = ($fileExtension === 'xls') 
            ? new \PhpOffice\PhpSpreadsheet\Reader\Xls() 
            : new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        
        $spreadsheet = $reader->load($inputFileName);

        // Ensure the correct sheet is selected
        $sheetName = 'Exception Stat.';
        if ($spreadsheet->sheetNameExists($sheetName)) {
            $sheet = $spreadsheet->getSheetByName($sheetName);
        } else {
            echo json_encode([
                'success' => false,
                'message' => "The sheet '$sheetName' does not exist in the uploaded file."
            ]);
            exit;
        }

        $dataRows = $sheet->toArray(null, true, true, true); // Read all rows as an array
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => "Error reading Excel file: " . $e->getMessage()
        ]);
        exit;
    }

    // Remove header row
    array_shift($dataRows);

    // Function to parse Excel dates
    function parseExcelDate($excelDate) {
        if (is_numeric($excelDate)) {
            $excelBaseDate = new DateTime('1900-01-01');
            $excelBaseDate->modify('+' . ((int)$excelDate - 2) . ' days'); // Adjust for Excel's date system
            return $excelBaseDate->format('Y-m-d');
        }
        return date('Y-m-d', strtotime($excelDate)); // Fallback for non-numeric dates
    }

    // Function to parse Excel times
    function parseExcelTime($excelTime) {
        if (is_numeric($excelTime)) {
            $seconds = round($excelTime * 86400); // Convert Excel time fraction to seconds
            $hours = floor($seconds / 3600);
            $seconds %= 3600;
            $minutes = floor($seconds / 60);
            $seconds %= 60;
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }
        return $excelTime; // Fallback for non-numeric times
    }

    // Function to format time
    function formatTime($excelTime) {
        if (is_numeric($excelTime)) {
            $seconds = round($excelTime * 86400); // Convert Excel time fraction to seconds
            $hours = floor($seconds / 3600);
            $seconds %= 3600;
            $minutes = floor($seconds / 60);
            $seconds %= 60;
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }
        return $excelTime; // Fallback for non-numeric times
    }

    // Process rows
    foreach ($dataRows as $row) {
        $rowCount++;

        // Ensure we have enough columns (adjust based on your Excel structure)
        if (count($row) < 8) {
            file_put_contents(__DIR__ . '/excel_debug.log', "Warning: Row $rowCount has less than 8 columns. Skipping.\n", FILE_APPEND);
            continue;
        }

        // Map columns (adjust based on your Excel structure)
        $empId = trim($row['A']); // Column A
        $name = trim($row['B']); // Column B
        $dept = trim($row['C']); // Column C
        $date = parseExcelDate(trim($row['D']));
        $amIn = parseExcelTime(trim($row['E']));
        $amOut = parseExcelTime(trim($row['F']));
        $pmIn = parseExcelTime(trim($row['G']));
        $pmOut = parseExcelTime(trim($row['H']));
        $late = trim($row['I']);
        $undertime = trim($row['L']); // Column L - UNDERTIME

        // Skip header rows
        if ($empId == 'ID' || $name == 'Name' || $dept == 'Department' || 
            strpos($empId, 'Stat.Date') !== false) {
            file_put_contents(__DIR__ . '/excel_debug.log', "Skipping header row: $empId, $name\n", FILE_APPEND);
            continue;
        }

        // Store employee and attendance data
        if (!empty($empId) && !empty($name)) {
            $employeeData[$empId] = [
                'name' => $name,
                'dept' => $dept
            ];
        }

        if (!empty($empId) && !empty($date)) {
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
                'late' => $late,
                'undertime' => $undertime,
                'row' => $rowCount
            ];
        }
    }
}

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Log database connection
    file_put_contents(__DIR__ . '/excel_debug.log', "Database connected successfully\n", FILE_APPEND);
    
    // Begin transaction
    $pdo->beginTransaction();
    
    // Two arrays to track data
    $employeeData = [];
    $attendanceData = [];
    $rowCount = 0;
    
    // Process rows
    foreach ($dataRows as $row) {
        $rowCount++;

        // Ensure we have enough columns (adjust based on your Excel structure)
        if (count($row) < 8) {
            file_put_contents(__DIR__ . '/excel_debug.log', "Warning: Row $rowCount has less than 8 columns. Skipping.\n", FILE_APPEND);
            continue;
        }

        // Map columns (adjust based on your Excel structure)
        $empId = trim($row['A']);
        $name = trim($row['B']);
        $dept = trim($row['C']);
        $date = trim($row['D']);
        $amIn = trim($row['E']);
        $amOut = trim($row['F']);
        $pmIn = trim($row['G']); 
        $late = trim($row['I']); 
        $undertime = trim($row['L']); // Column L - UNDERTIME
        // Skip header rows
        if ($empId == 'ID' || $name == 'Name' || $dept == 'Department' || 
            strpos($empId, 'Stat.Date') !== false) {
            file_put_contents(__DIR__ . '/excel_debug.log', "Skipping header row: $empId, $name\n", FILE_APPEND);
            continue;
        }

        // Format date and time (reuse existing logic)
        if (!empty($date)) {
            if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $date, $matches)) {
                $month = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                $day = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
                $year = $matches[3];
                $date = "$year-$month-$day";
            } else if (is_numeric($date)) {
                $excelBaseDate = new DateTime('1900-01-01');
                $excelBaseDate->modify('+' . ($date - 2) . ' days');
                $date = $excelBaseDate->format('Y-m-d');
            } else if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                $date = date('Y-m-d', strtotime($date));
            }
        }

        // Only format time if there's actual data
        $amIn = !empty($row['E']) ? formatTime($amIn) : null;
        $amOut = !empty($row['F']) ? formatTime($amOut) : null;
        $pmIn = !empty($row['G']) ? formatTime($pmIn) : null;
        $pmOut = !empty($row['H']) ? formatTime($pmOut) : null;

        // Store employee and attendance data (reuse existing logic)
        if (!empty($empId) && !empty($name)) {
            $employeeData[$empId] = [
                'name' => $name,
                'dept' => $dept
            ];
        }

        if (!empty($empId) && !empty($date)) {
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
                'late' => $late,
                'undertime' => $undertime,
                'row' => $rowCount
            ];
        }
    }
    
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
        $late = $record['late'];
        $undertime = $record['undertime'];
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
                $stmt = $pdo->prepare("UPDATE emp_rec SET AM_IN = ?, AM_OUT = ?, PM_IN = ?, PM_OUT = ?, LATE = ?, UNDERTIME = ? WHERE EMP_ID = ? AND DATE = ?");
                $stmt->execute([$amIn, $amOut, $pmIn, $pmOut, $late, $undertime, $empId, $date]);
                $attendanceUpdated++;
                file_put_contents(__DIR__ . '/excel_debug.log', "Updated attendance: ID=$empId, Date=$date\n", FILE_APPEND);
            } else {
                // Skip this record as user wanted to skip duplicates
                $attendanceSkipped++;
                file_put_contents(__DIR__ . '/excel_debug.log', "Skipping duplicate attendance: ID=$empId, Date=$date\n", FILE_APPEND);
            }
        } else {
            // No duplicate - insert new record with REAL Excel data
            $stmt = $pdo->prepare("INSERT INTO emp_rec (EMP_ID, DATE, AM_IN, AM_OUT, PM_IN, PM_OUT, LATE, UNDERTIME) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$empId, $date, $amIn, $amOut, $pmIn, $pmOut, $late, $undertime]);
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
