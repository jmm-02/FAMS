<?php
// Define debug mode
define('DEBUG_MODE', true);

// Include time helper functions
require_once __DIR__ . '/time_helper.php';

// Clear the debug log
file_put_contents(__DIR__ . '/excel_debug.log', "Starting Exception Report import...\n");

// Function to process time values into proper MySQL format
function processTimeValue($timeValue) {
    // If empty, return NULL
    if (empty($timeValue) || $timeValue === 'NULL') {
        return null;
    }
    
    // If it's already in HH:MM:SS format, return as is
    if (preg_match('/^\d{1,2}:\d{2}:\d{2}$/', $timeValue)) {
        return $timeValue;
    }
    
    // If it's in HH:MM format, add seconds
    if (preg_match('/^\d{1,2}:\d{2}$/', $timeValue)) {
        return $timeValue . ':00';
    }
    
    // If it's numeric (like 18.31 or 1831)
    if (is_numeric($timeValue)) {
        // If it has a decimal point (like 18.31)
        if (strpos($timeValue, '.') !== false) {
            $parts = explode('.', $timeValue);
            $hours = intval($parts[0]);
            $minutes = isset($parts[1]) ? intval(($parts[1] / 100) * 60) : 0;
            return sprintf('%02d:%02d:00', $hours, $minutes);
        } else {
            // If it's just a number (like 1831), parse it as HHMM
            return sprintf('%02d:%02d:00', floor($timeValue / 100), $timeValue % 100);
        }
    }
    
    // Try to use formatTimeValue as fallback
    $formatted = formatTimeValue($timeValue);
    return $formatted;
}

// Database connection parameters
$host = 'localhost';
$dbname = 'famsattendance';
$username = 'root';
$password = '';

try {
    // Connect to the database
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if a file was uploaded
    if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] != UPLOAD_ERR_OK) {
        throw new Exception("No file uploaded or upload error.");
    }
    
    $uploadedFile = $_FILES['excel_file']['tmp_name'];
    $originalFileName = $_FILES['excel_file']['name'];
    
    file_put_contents(__DIR__ . '/excel_debug.log', "Processing file: $originalFileName\n", FILE_APPEND);
    file_put_contents(__DIR__ . '/excel_debug.log', "Reading CSV file: $uploadedFile\n", FILE_APPEND);
    
    // Initialize arrays to store data
    $employees = [];
    $attendanceData = [];
    
    // Open the CSV file directly
    if (($handle = fopen($uploadedFile, "r")) !== FALSE) {
        $row = 0;
        $dataStarted = false;
        
        // Read each row
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $row++;
            
            // Log the row for debugging
            file_put_contents(__DIR__ . '/excel_debug.log', "Row $row: " . implode(", ", $data) . "\n", FILE_APPEND);
            
            // Skip empty rows
            if (empty($data) || count(array_filter($data)) == 0) {
                continue;
            }
            
            // Check if this is the header row with "Exception Statistic Report"
            if (isset($data[0]) && stripos($data[0], 'Exception Statistic Report') !== false) {
                file_put_contents(__DIR__ . '/excel_debug.log', "Found Exception Statistic Report header\n", FILE_APPEND);
                continue;
            }
            
            // Skip the "Stat.Date:" row
            if (isset($data[0]) && stripos($data[0], 'Stat.Date:') !== false) {
                file_put_contents(__DIR__ . '/excel_debug.log', "Skipping Stat.Date row\n", FILE_APPEND);
                continue;
            }
            
            // Skip the header row with column names
            if (isset($data[0]) && $data[0] === 'ID') {
                file_put_contents(__DIR__ . '/excel_debug.log', "Skipping column headers row\n", FILE_APPEND);
                continue;
            }
            
            // Process data rows - looking for numeric ID in first column
            if (count($data) >= 8 && is_numeric(trim($data[0]))) {
                $id = trim($data[0]);      // Column A = ID (EMP_ID)
                $name = trim($data[1]);    // Column B = Name
                $dept = trim($data[2]);    // Column C = Department
                $date = trim($data[3]);    // Column D = Date
                
                // Get time values from columns E, F, G, H
                $amIn = isset($data[4]) ? trim($data[4]) : '';   // Column E = AM_IN (First time zone On-duty)
                $amOut = isset($data[5]) ? trim($data[5]) : '';  // Column F = AM_OUT (First time zone Off-duty)
                $pmIn = isset($data[6]) ? trim($data[6]) : '';   // Column G = PM_IN (Second time zone On-duty)
                $pmOut = isset($data[7]) ? trim($data[7]) : '';  // Column H = PM_OUT (Second time zone Off-duty)
                
                file_put_contents(__DIR__ . '/excel_debug.log', "Found data row: ID=$id, Name=$name, Dept=$dept, Date=$date, AM_IN=$amIn, AM_OUT=$amOut, PM_IN=$pmIn, PM_OUT=$pmOut\n", FILE_APPEND);
                
                // Store employee data
                if (!isset($employees[$id]) && !empty($name)) {
                    $employees[$id] = [
                        'name' => $name,
                        'dept' => $dept
                    ];
                    file_put_contents(__DIR__ . '/excel_debug.log', "Added employee: ID=$id, Name=$name, Dept=$dept\n", FILE_APPEND);
                }
                
                // Format date if needed
                if (!empty($date) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                    // Try to parse various date formats
                    $timestamp = strtotime($date);
                    if ($timestamp !== false) {
                        $date = date('Y-m-d', $timestamp);
                    }
                }
                
                // Process all time values to ensure proper format
                $amIn = processTimeValue($amIn);
                $amOut = processTimeValue($amOut);
                $pmIn = processTimeValue($pmIn);
                $pmOut = processTimeValue($pmOut);
                
                // Store attendance data
                if (!empty($date)) {
                    $attendanceData[] = [
                        'id' => $id,
                        'date' => $date,
                        'amIn' => $amIn,
                        'amOut' => $amOut,
                        'pmIn' => $pmIn,
                        'pmOut' => $pmOut
                    ];
                    file_put_contents(__DIR__ . '/excel_debug.log', "Added attendance: ID=$id, Date=$date, AM_IN=$amIn, AM_OUT=$amOut, PM_IN=$pmIn, PM_OUT=$pmOut\n", FILE_APPEND);
                }
            }
        }
        fclose($handle);
    } else {
        throw new Exception("Failed to open CSV file for reading.");
    }
    
    // Check if we found any data
    if (empty($employees)) {
        throw new Exception("No employee data found in the Excel file. Please check the file format.");
    }
    
    if (empty($attendanceData)) {
        throw new Exception("No attendance data found in the Excel file. Please check the file format.");
    }
    
    file_put_contents(__DIR__ . '/excel_debug.log', "Found " . count($employees) . " employees and " . count($attendanceData) . " attendance records\n", FILE_APPEND);
    
    // Begin transaction
    $conn->beginTransaction();
    
    try {
        // Step 1: Process employee data
        $employeesInserted = 0;
        $employeesUpdated = 0;
        
        foreach ($employees as $id => $data) {
            // Check if employee exists
            $stmt = $conn->prepare("SELECT emp_id FROM employees WHERE emp_id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() > 0) {
                // Update existing employee
                $stmt = $conn->prepare("UPDATE employees SET name = ?, department = ? WHERE emp_id = ?");
                $result = $stmt->execute([$data['name'], $data['dept'], $id]);
                if ($result) {
                    $employeesUpdated++;
                    file_put_contents(__DIR__ . '/excel_debug.log', "Updated employee: ID=$id\n", FILE_APPEND);
                }
            } else {
                // Insert new employee
                $stmt = $conn->prepare("INSERT INTO employees (emp_id, name, department) VALUES (?, ?, ?)");
                $result = $stmt->execute([$id, $data['name'], $data['dept']]);
                if ($result) {
                    $employeesInserted++;
                    file_put_contents(__DIR__ . '/excel_debug.log', "Inserted employee: ID=$id\n", FILE_APPEND);
                }
            }
        }
        
        // Step 2: Process attendance data
        $attendanceInserted = 0;
        $attendanceUpdated = 0;
        
        foreach ($attendanceData as $attendance) {
            $id = $attendance['id'];
            $date = $attendance['date'];
            $amIn = $attendance['amIn'];
            $amOut = $attendance['amOut'];
            $pmIn = $attendance['pmIn'];
            $pmOut = $attendance['pmOut'];
            
            // Check if attendance record exists
            $stmt = $conn->prepare("SELECT * FROM attendance WHERE emp_id = ? AND date = ?");
            $stmt->execute([$id, $date]);
            
            if ($stmt->rowCount() > 0) {
                // Update existing attendance record
                $stmt = $conn->prepare("UPDATE attendance SET am_in = ?, am_out = ?, pm_in = ?, pm_out = ? WHERE emp_id = ? AND date = ?");
                $result = $stmt->execute([$amIn, $amOut, $pmIn, $pmOut, $id, $date]);
                if ($result) {
                    $attendanceUpdated++;
                    file_put_contents(__DIR__ . '/excel_debug.log', "Updated attendance: ID=$id, Date=$date\n", FILE_APPEND);
                }
            } else {
                // Insert new attendance record
                $stmt = $conn->prepare("INSERT INTO attendance (emp_id, date, am_in, am_out, pm_in, pm_out) VALUES (?, ?, ?, ?, ?, ?)");
                $result = $stmt->execute([$id, $date, $amIn, $amOut, $pmIn, $pmOut]);
                if ($result) {
                    $attendanceInserted++;
                    file_put_contents(__DIR__ . '/excel_debug.log', "Inserted attendance: ID=$id, Date=$date\n", FILE_APPEND);
                }
            }
        }
        
        // Commit transaction
        $conn->commit();
        
        $message = "Successfully processed $employeesInserted new employees, updated $employeesUpdated employees, inserted $attendanceInserted attendance records, and updated $attendanceUpdated attendance records.";
        file_put_contents(__DIR__ . '/excel_debug.log', $message . "\n", FILE_APPEND);
        
        // Return success response
        echo json_encode([
            'success' => true,
            'message' => $message
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    // Log error
    file_put_contents(__DIR__ . '/excel_debug.log', "ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
    
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => "Error: " . $e->getMessage()
    ]);
}
?>
