<?php
// Define debug mode
define('DEBUG_MODE', true);

// Database connection parameters
$host = 'localhost';
$dbname = 'famsattendance';
$username = 'root';
$password = '';

// Clear the debug log
file_put_contents(__DIR__ . '/excel_debug.log', "Starting direct import...\n");

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Begin transaction
    $pdo->beginTransaction();
    
    // Sample employee data from your Excel file
    $employees = [
        '1' => ['name' => 'JM', 'dept' => 'Company']
    ];
    
    // Sample attendance data from your Excel file
    $attendanceData = [
        ['id' => '1', 'date' => '2025-04-01', 'amIn' => '08:30', 'amOut' => '', 'pmIn' => '', 'pmOut' => '17:30'],
        ['id' => '1', 'date' => '2025-04-02', 'amIn' => '08:30', 'amOut' => '', 'pmIn' => '', 'pmOut' => '17:30'],
        ['id' => '1', 'date' => '2025-04-03', 'amIn' => '08:30', 'amOut' => '', 'pmIn' => '', 'pmOut' => '17:30']
    ];
    
    // Counters
    $updated = 0;
    $added = 0;
    $attendanceRecords = 0;
    
    // Process employee data
    file_put_contents(__DIR__ . '/excel_debug.log', "Processing " . count($employees) . " employee records...\n", FILE_APPEND);
    
    foreach ($employees as $id => $data) {
        // Check if employee exists
        $stmt = $pdo->prepare("SELECT * FROM emp_info WHERE ID = :id");
        $stmt->execute(['id' => $id]);
        $existingEmployee = $stmt->fetch(PDO::FETCH_ASSOC);
        
        file_put_contents(__DIR__ . '/excel_debug.log', "Employee check: ID=$id, " . ($existingEmployee ? "EXISTS" : "NEW") . "\n", FILE_APPEND);
        
        if ($existingEmployee) {
            // Only update if name or department has changed
            if ($existingEmployee['Name'] !== $data['name'] || $existingEmployee['Dept.'] !== $data['dept']) {
                $stmt = $pdo->prepare("UPDATE emp_info SET Name = :name, `Dept.` = :dept WHERE ID = :id");
                $stmt->execute([
                    'name' => $data['name'],
                    'dept' => $data['dept'],
                    'id' => $id
                ]);
                
                $updated++;
                file_put_contents(__DIR__ . '/excel_debug.log', "Updated employee: ID=$id, Name={$data['name']}, Dept={$data['dept']}\n", FILE_APPEND);
            } else {
                file_put_contents(__DIR__ . '/excel_debug.log', "Employee unchanged: ID=$id, Name={$data['name']}, Dept={$data['dept']}\n", FILE_APPEND);
            }
        } else {
            // Add new employee
            $stmt = $pdo->prepare("INSERT INTO emp_info (ID, Name, `Dept.`) VALUES (:id, :name, :dept)");
            $stmt->execute([
                'id' => $id,
                'name' => $data['name'],
                'dept' => $data['dept']
            ]);
            
            $added++;
            file_put_contents(__DIR__ . '/excel_debug.log', "Added new employee: ID=$id, Name={$data['name']}, Dept={$data['dept']}\n", FILE_APPEND);
        }
    }
    
    // Process attendance data
    file_put_contents(__DIR__ . '/excel_debug.log', "Processing " . count($attendanceData) . " attendance records...\n", FILE_APPEND);
    
    foreach ($attendanceData as $record) {
        // Check if a record already exists for this employee and date
        $stmt = $pdo->prepare("SELECT * FROM emp_rec WHERE EMP_ID = :emp_id AND DATE = :date");
        $stmt->execute([
            'emp_id' => $record['id'],
            'date' => $record['date']
        ]);
        $existingRecord = $stmt->fetch(PDO::FETCH_ASSOC);
        
        file_put_contents(__DIR__ . '/excel_debug.log', "Attendance check: ID={$record['id']}, Date={$record['date']}, " . 
                         ($existingRecord ? "EXISTS" : "NEW") . "\n", FILE_APPEND);
        
        if ($existingRecord) {
            // Prepare update query parts
            $updateParts = [];
            $params = [
                'emp_id' => $record['id'],
                'date' => $record['date']
            ];
            
            // Only update non-empty values
            if (!empty($record['amIn'])) {
                $updateParts[] = "AM_IN = :am_in";
                $params['am_in'] = $record['amIn'];
            }
            
            if (!empty($record['amOut'])) {
                $updateParts[] = "AM_OUT = :am_out";
                $params['am_out'] = $record['amOut'];
            }
            
            if (!empty($record['pmIn'])) {
                $updateParts[] = "PM_IN = :pm_in";
                $params['pm_in'] = $record['pmIn'];
            }
            
            if (!empty($record['pmOut'])) {
                $updateParts[] = "PM_OUT = :pm_out";
                $params['pm_out'] = $record['pmOut'];
            }
            
            // Only update if there are values to update
            if (!empty($updateParts)) {
                $updateQuery = "UPDATE emp_rec SET " . implode(", ", $updateParts) . " WHERE EMP_ID = :emp_id AND DATE = :date";
                $stmt = $pdo->prepare($updateQuery);
                $stmt->execute($params);
                
                $attendanceRecords++;
                file_put_contents(__DIR__ . '/excel_debug.log', "Updated attendance: ID={$record['id']}, Date={$record['date']}\n", FILE_APPEND);
            }
        } else {
            // Prepare columns and values for insert
            $columns = ["EMP_ID", "DATE"];
            $placeholders = [":emp_id", ":date"];
            $params = [
                'emp_id' => $record['id'],
                'date' => $record['date']
            ];
            
            // Only include non-empty values
            if (!empty($record['amIn'])) {
                $columns[] = "AM_IN";
                $placeholders[] = ":am_in";
                $params['am_in'] = $record['amIn'];
            }
            
            if (!empty($record['amOut'])) {
                $columns[] = "AM_OUT";
                $placeholders[] = ":am_out";
                $params['am_out'] = $record['amOut'];
            }
            
            if (!empty($record['pmIn'])) {
                $columns[] = "PM_IN";
                $placeholders[] = ":pm_in";
                $params['pm_in'] = $record['pmIn'];
            }
            
            if (!empty($record['pmOut'])) {
                $columns[] = "PM_OUT";
                $placeholders[] = ":pm_out";
                $params['pm_out'] = $record['pmOut'];
            }
            
            // Insert new record with only non-empty values
            $insertQuery = "INSERT INTO emp_rec (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $placeholders) . ")";
            $stmt = $pdo->prepare($insertQuery);
            $stmt->execute($params);
            
            $attendanceRecords++;
            file_put_contents(__DIR__ . '/excel_debug.log', "Added attendance: ID={$record['id']}, Date={$record['date']}\n", FILE_APPEND);
        }
    }
    
    // Commit transaction
    $pdo->commit();
    
    echo "Import completed successfully. Employees: $updated updated, $added added. Attendance records: $attendanceRecords processed.";
    file_put_contents(__DIR__ . '/excel_debug.log', "Import completed. Employees: $updated updated, $added added. Attendance: $attendanceRecords records processed.\n", FILE_APPEND);
    
} catch (PDOException $e) {
    // Roll back transaction if an error occurred
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    echo "Error: " . $e->getMessage();
    file_put_contents(__DIR__ . '/excel_debug.log', "ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
}
?>
