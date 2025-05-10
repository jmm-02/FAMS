<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
include '../includes/db_connect.php';

// Get the POST data
$data = json_decode(file_get_contents('php://input'), true);

// Log the received data
error_log("Received data: " . print_r($data, true));

if (!isset($data['emp_id']) || !isset($data['date'])) {
    error_log("Missing parameters - emp_id: " . (isset($data['emp_id']) ? $data['emp_id'] : 'not set') . 
              ", date: " . (isset($data['date']) ? $data['date'] : 'not set'));
    echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
    exit;
}

$emp_id = $data['emp_id'];
$date = $data['date'];
$sl_value = isset($data['sl_value']) ? (int)$data['sl_value'] : 1;

error_log("Processing SL update: emp_id=$emp_id, date=$date, sl_value=$sl_value");

try {
    // Log the query parameters
    error_log("Updating SL for emp_id: $emp_id, date: $date, new value: $sl_value");
    
    // Check if OB is set, if marking as SL
    if ($sl_value == 1) {
        $checkOB = $pdo->prepare("SELECT OB FROM emp_rec WHERE EMP_ID = :emp_id AND DATE = :date");
        $checkOB->execute([':emp_id' => $emp_id, ':date' => $date]);
        $ob_status = $checkOB->fetchColumn();
        
        if ($ob_status == 1) {
            echo json_encode(['success' => false, 'error' => 'Cannot mark as SL because this record is already marked as OB']);
            exit;
        }
    }
    
    // Update the SL status in the database
    $stmt = $pdo->prepare("UPDATE emp_rec SET SL = :sl_value WHERE EMP_ID = :emp_id AND DATE = :date");
    if (!$stmt) {
        error_log("Prepare failed: " . $pdo->errorInfo()[2]);
        throw new Exception("Prepare failed: " . $pdo->errorInfo()[2]);
    }
    
    if ($stmt->execute([':sl_value' => $sl_value, ':emp_id' => $emp_id, ':date' => $date])) {
        $affected_rows = $stmt->rowCount();
        error_log("Update successful. Affected rows: " . $affected_rows);
        echo json_encode(['success' => true, 'affected_rows' => $affected_rows]);
    } else {
        error_log("Execute failed: " . $pdo->errorInfo()[2]);
        echo json_encode(['success' => false, 'error' => 'Failed to update SL status: ' . $pdo->errorInfo()[2]]);
    }
} catch (Exception $e) {
    error_log("Exception occurred: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?> 