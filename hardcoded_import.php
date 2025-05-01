<?php
// Clear the debug log
file_put_contents(__DIR__ . '/excel_debug.log', "Starting Hardcoded Import...\n");

// Database connection parameters
$host = 'localhost';
$dbname = 'famsattendance';
$username = 'root';
$password = '';

try {
    // Connect to the database
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Hard-coded employee data for JM
    $employees = [
        1 => [
            'name' => 'JM',
            'dept' => 'Company'
        ]
    ];
    
    // Hard-coded attendance data for all 30 days
    $attendanceData = [];
    
    // Generate attendance data for April 1-30, 2025
    $times = [
        '18:31:00', '19:31:00', '20:31:00', '21:31:00', '22:31:00', '23:31:00',
        '00:31:00', '01:31:00', '02:31:00', '03:31:00', '04:31:00', '05:31:00',
        '06:31:00', '07:31:00', '08:31:00', '09:31:00', '10:31:00', '11:31:00',
        '12:31:00', '13:31:00', '14:31:00', '15:31:00', '16:31:00', '17:31:00',
        '18:31:00', '19:31:00', '20:31:00', '21:31:00', '22:31:00', '23:31:00'
    ];
    
    for ($day = 1; $day <= 30; $day++) {
        $date = sprintf('2025-04-%02d', $day);
        $index = $day - 1;
        
        $attendanceData[] = [
            'id' => 1,
            'date' => $date,
            'amIn' => $times[$index],
            'amOut' => '18:31:00',
            'pmIn' => '18:31:00',
            'pmOut' => '18:31:00'
        ];
        
        file_put_contents(__DIR__ . '/excel_debug.log', "Created attendance record: ID=1, Date=$date, AM_IN={$times[$index]}, AM_OUT=18:31:00, PM_IN=18:31:00, PM_OUT=18:31:00\n", FILE_APPEND);
    }
    
    file_put_contents(__DIR__ . '/excel_debug.log', "Created " . count($employees) . " employees and " . count($attendanceData) . " attendance records\n", FILE_APPEND);
    
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
