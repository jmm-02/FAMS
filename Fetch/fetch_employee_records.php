<?php
// Database connection
include '../includes/db_connect.php';
require '../includes/session_handler.php';

// Get employee ID from request
$emp_id = isset($_GET['emp_id']) ? $_GET['emp_id'] : null;

// Validate employee ID
if (!$emp_id) {
    echo json_encode(['error' => 'Employee ID is required']);
    exit;
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get employee info
    $empQuery = "SELECT ID as emp_id, Name, DEPT as department, STATUS as status 
                FROM emp_info 
                WHERE ID = :emp_id";
    $empStmt = $pdo->prepare($empQuery);
    $empStmt->bindParam(':emp_id', $emp_id);
    $empStmt->execute();
    $employee = $empStmt->fetch(PDO::FETCH_ASSOC);

    if (!$employee) {
        echo json_encode(['error' => 'Employee not found']);
        exit;
    }

    // Get attendance records for the employee
    $recQuery = "SELECT ID as record_id, DATE as date, 
                AM_IN as am_in, AM_OUT as am_out, 
                PM_IN as pm_in, PM_OUT as pm_out, 
                LATE as late, 
                UNDERTIME as undertime,
                `NOTE` as note,
                OB,
                SL
                FROM emp_rec 
                WHERE EMP_ID = :emp_id
                ORDER BY DATE DESC";
    $recStmt = $pdo->prepare($recQuery);
    $recStmt->bindParam(':emp_id', $emp_id);
    $recStmt->execute();
    $records = $recStmt->fetchAll(PDO::FETCH_ASSOC);

    // Combine employee info with attendance records
    $result = [
        'employee' => $employee,
        'records' => $records
    ];
    
    echo json_encode($result);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
