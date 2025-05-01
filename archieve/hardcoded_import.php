<?php
// Set proper headers for JSON response
header('Content-Type: application/json');

// Clear the debug log
file_put_contents(__DIR__ . '/excel_debug.log', "Starting Hardcoded Import...\n");

// Database connection parameters
$host = 'localhost';
$dbname = 'famsattendance';
$username = 'root';
$password = '';

// Get POST parameters or use defaults
$emp_id = isset($_POST['emp_id']) ? $_POST['emp_id'] : '1';  // ID is VARCHAR in database
$emp_name = isset($_POST['emp_name']) ? $_POST['emp_name'] : 'JM';
$emp_dept = isset($_POST['emp_dept']) ? $_POST['emp_dept'] : 'Company';
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '2025-04-01';
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '2025-04-30';

file_put_contents(__DIR__ . '/excel_debug.log', "Processing import for Employee ID: $emp_id, Name: $emp_name, Department: $emp_dept\n", FILE_APPEND);

try {
    // Connect to the database
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Handle employee data dynamically
    $employees = [
        $emp_id => [
            'name' => $emp_name,
            'dept' => $emp_dept
        ]
    ];
    
    // Hard-coded attendance data
    $attendanceData = [];
    
    // Parse start and end dates
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $interval = new DateInterval('P1D'); // 1 day interval
    $period = new DatePeriod($start, $interval, $end->modify('+1 day'));
    
    // Generate attendance data for date range
    $times = [
        '18:31:00', '19:31:00', '20:31:00', '21:31:00', '22:31:00', '23:31:00',
        '00:31:00', '01:31:00', '02:31:00', '03:31:00', '04:31:00', '05:31:00',
        '06:31:00', '07:31:00', '08:31:00', '09:31:00', '10:31:00', '11:31:00',
        '12:31:00', '13:31:00', '14:31:00', '15:31:00', '16:31:00', '17:31:00',
        '18:31:00', '19:31:00', '20:31:00', '21:31:00', '22:31:00', '23:31:00'
    ];
    
    $index = 0;
    foreach ($period as $day) {
        $date = $day->format('Y-m-d');
        $time_index = $index % count($times); // Cycle through times if more days than times
        
        $attendanceData[] = [
            'id' => $emp_id,
            'date' => $date,
            'amIn' => $times[$time_index],
            'amOut' => '18:31:00',
            'pmIn' => '18:31:00',
            'pmOut' => '18:31:00'
        ];
        
        file_put_contents(__DIR__ . '/excel_debug.log', "Created attendance record: ID=$emp_id, Date=$date, AM_IN={$times[$time_index]}, AM_OUT=18:31:00, PM_IN=18:31:00, PM_OUT=18:31:00\n", FILE_APPEND);
        $index++;
    }
    
    file_put_contents(__DIR__ . '/excel_debug.log', "Created " . count($employees) . " employees and " . count($attendanceData) . " attendance records\n", FILE_APPEND);
    
    // Begin transaction
    $conn->beginTransaction();
    
    try {
        // Log the import operation
        file_put_contents(__DIR__ . '/excel_debug.log', "Starting import operation for Employee ID: $emp_id\n", FILE_APPEND);

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
                    file_put_contents(__DIR__ . '/excel_debug.log', "Updated employee: ID=$id, Name={$data['name']}, Dept={$data['dept']}\n", FILE_APPEND);
                }
            } else {
                // Insert new employee
                $stmt = $conn->prepare("INSERT INTO emp_info (ID, NAME, DEPT, STATUS) VALUES (?, ?, ?, ?)");
                $result = $stmt->execute([(string)$id, $data['name'], $data['dept'], '']);
                if ($result) {
                    $employeesInserted++;
                    file_put_contents(__DIR__ . '/excel_debug.log', "Inserted NEW employee: ID=$id, Name={$data['name']}, Dept={$data['dept']}\n", FILE_APPEND);
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
                // Insert new attendance record - don't need to specify ID as it's auto-increment
                $stmt = $conn->prepare("INSERT INTO emp_rec (EMP_ID, DATE, AM_IN, AM_OUT, PM_IN, PM_OUT) VALUES (?, ?, ?, ?, ?, ?)");
                $result = $stmt->execute([(string)$id, $date, $amIn, $amOut, $pmIn, $pmOut]);
                if ($result) {
                    $attendanceInserted++;
                    file_put_contents(__DIR__ . '/excel_debug.log', "Inserted attendance: EMP_ID=$id, Date=$date\n", FILE_APPEND);
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
            'message' => $message,
            'data' => [
                'employeeId' => $emp_id,
                'employeeName' => $emp_name,
                'department' => $emp_dept,
                'recordsProcessed' => count($attendanceData),
                'dateRange' => "$start_date to $end_date"
            ]
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
        'message' => "Error: " . $e->getMessage(),
        'data' => [
            'employeeId' => $emp_id,
            'employeeName' => $emp_name,
            'department' => $emp_dept
        ]
    ]);
}
?>
