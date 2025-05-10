<?php
// Clear the debug log
file_put_contents(__DIR__ . '/excel_debug.log', "Starting Simple Import...\n");

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
    
    // Initialize arrays to store data
    $employees = [];
    $attendanceData = [];
    
    // Read the CSV file directly
    $csvContent = file_get_contents($uploadedFile);
    $lines = explode("\n", $csvContent);
    
    // Flag to track if we've found data rows
    $foundData = false;
    $headerFound = false;
    
    foreach ($lines as $lineNumber => $line) {
        // Skip empty lines
        if (empty(trim($line))) {
            continue;
        }
        
        // Log the line for debugging
        file_put_contents(__DIR__ . '/excel_debug.log', "Line " . ($lineNumber + 1) . ": $line\n", FILE_APPEND);
        
        // Check if this is the header line
        if (strpos($line, 'Exception Statistic Report') !== false) {
            $headerFound = true;
            file_put_contents(__DIR__ . '/excel_debug.log', "Found Exception Statistic Report header\n", FILE_APPEND);
            continue;
        }
        
        // Skip header rows
        if ($headerFound && !$foundData) {
            // Check if this is a data row by looking for a pattern that matches your data
            // For any row that starts with a number followed by a comma and has data in the expected format
            if (preg_match('/^\d+,\s*[^,]+,\s*[^,]+,\s*\d{4}-\d{2}-\d{2}/', $line) || 
                preg_match('/^1,\s*JM,\s*Company,\s*\d{4}-\d{2}-\d{2}/', $line)) {
                $foundData = true;
                file_put_contents(__DIR__ . '/excel_debug.log', "Found data row: $line\n", FILE_APPEND);
            } else {
                // Skip header rows
                continue;
            }
        }
        
        // Process data rows
        if ($foundData) {
            $data = str_getcsv($line);
            
            // Skip if we don't have enough columns
            if (count($data) < 8) {
                continue;
            }
            
            $id = trim($data[0]);      // Column A = ID (EMP_ID)
            $name = trim($data[1]);    // Column B = Name
            $dept = trim($data[2]);    // Column C = Department
            $date = trim($data[3]);    // Column D = Date
            
            // Get time values from columns E, F, G, H
            $amIn = isset($data[4]) ? trim($data[4]) : '';   // Column E = AM_IN
            $amOut = isset($data[5]) ? trim($data[5]) : '';  // Column F = AM_OUT
            $pmIn = isset($data[6]) ? trim($data[6]) : '';   // Column G = PM_IN
            $pmOut = isset($data[7]) ? trim($data[7]) : '';  // Column H = PM_OUT
            
            // Skip if ID is empty or not numeric
            if (empty($id) || !is_numeric($id)) {
                continue;
            }
            
            // For debugging - log all data rows we're processing
            file_put_contents(__DIR__ . '/excel_debug.log', "Processing row: ID=$id, Name=$name, Dept=$dept, Date=$date, AM_IN=$amIn, AM_OUT=$amOut, PM_IN=$pmIn, PM_OUT=$pmOut\n", FILE_APPEND);
            
            file_put_contents(__DIR__ . '/excel_debug.log', "Processing data row: ID=$id, Name=$name, Dept=$dept, Date=$date, AM_IN=$amIn, AM_OUT=$amOut, PM_IN=$pmIn, PM_OUT=$pmOut\n", FILE_APPEND);
            
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
            
            // Format time values
            $amIn = !empty($amIn) ? $amIn . ':00' : null;
            $amOut = !empty($amOut) ? $amOut . ':00' : null;
            $pmIn = !empty($pmIn) ? $pmIn . ':00' : null;
            $pmOut = !empty($pmOut) ? $pmOut . ':00' : null;
            
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
    
    // Check if we found any data
    if (empty($employees)) {
        throw new Exception("No employee data found in the CSV file. Please check the file format.");
    }
    
    if (empty($attendanceData)) {
        throw new Exception("No attendance data found in the CSV file. Please check the file format.");
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
            $stmt = $conn->prepare("SELECT ID FROM emp_info WHERE ID = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() > 0) {
                // Update existing employee
                $stmt = $conn->prepare("UPDATE emp_info SET NAME = ?, DEPT = ? WHERE ID = ?");
                $result = $stmt->execute([$data['name'], $data['dept'], $id]);
                if ($result) {
                    $employeesUpdated++;
                    file_put_contents(__DIR__ . '/excel_debug.log', "Updated employee: ID=$id\n", FILE_APPEND);
                }
            } else {
                // Insert new employee
                $stmt = $conn->prepare("INSERT INTO emp_info (ID, NAME, DEPT) VALUES (?, ?, ?)");
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
            $stmt = $conn->prepare("SELECT * FROM emp_rec WHERE EMP_ID = ? AND DATE = ?");
            $stmt->execute([$id, $date]);
            
            if ($stmt->rowCount() > 0) {
                // Update existing attendance record
                $stmt = $conn->prepare("UPDATE emp_rec SET AM_IN = ?, AM_OUT = ?, PM_IN = ?, PM_OUT = ? WHERE EMP_ID = ? AND DATE = ?");
                $result = $stmt->execute([$amIn, $amOut, $pmIn, $pmOut, $id, $date]);
                if ($result) {
                    $attendanceUpdated++;
                    file_put_contents(__DIR__ . '/excel_debug.log', "Updated attendance: ID=$id, Date=$date\n", FILE_APPEND);
                }
            } else {
                // Insert new attendance record
                $stmt = $conn->prepare("INSERT INTO emp_rec (EMP_ID, DATE, AM_IN, AM_OUT, PM_IN, PM_OUT) VALUES (?, ?, ?, ?, ?, ?)");
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
