<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = ""; // Change if needed
$dbname = "famsattendance"; // Change if needed

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// Get and sanitize POST data
$EMP_ID     = isset($_POST['emp_id']) ? trim($_POST['emp_id']) : '';
$FIRST_NAME = isset($_POST['FIRST_NAME']) ? trim($_POST['FIRST_NAME']) : '';
$LAST_NAME  = isset($_POST['LAST_NAME']) ? trim($_POST['LAST_NAME']) : '';
$STATUS     = isset($_POST['STATUS']) ? trim($_POST['STATUS']) : '';
$POSITION   = isset($_POST['POSITION']) ? trim($_POST['POSITION']) : '';
$PIN_CODE   = isset($_POST['PIN_CODE']) ? trim($_POST['PIN_CODE']) : '';

if (!$EMP_ID || !$FIRST_NAME || !$LAST_NAME || !$STATUS || !$POSITION || !$PIN_CODE) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid or missing input',
        'debug' => [
            'emp_id' => $EMP_ID,
            'first_name' => $FIRST_NAME,
            'last_name' => $LAST_NAME,
            'status' => $STATUS,
            'position' => $POSITION,
            'pin_code' => $PIN_CODE
        ]
    ]);
    exit;
}

$conn->begin_transaction();

try {
    // Update emp_info
    $stmt1 = $conn->prepare("UPDATE emp_info SET FIRST_NAME=?, LAST_NAME=?, STATUS=? WHERE ID=?");
    $stmt1->bind_param("ssss", $FIRST_NAME, $LAST_NAME, $STATUS, $EMP_ID);
    $stmt1->execute();
    $affected1 = $stmt1->affected_rows;
    $stmt1->close();

    // Update emp_position
    $stmt2 = $conn->prepare("UPDATE emp_position SET POSITION=? WHERE EMP_ID=?");
    $stmt2->bind_param("ss", $POSITION, $EMP_ID);
    $stmt2->execute();
    $affected2 = $stmt2->affected_rows;
    $stmt2->close();

    // Update pass_key
    $stmt3 = $conn->prepare("UPDATE pass_key SET PIN_CODE=? WHERE EMP_ID=?");
    $stmt3->bind_param("ss", $PIN_CODE, $EMP_ID);
    $stmt3->execute();
    $affected3 = $stmt3->affected_rows;
    $stmt3->close();

    // Check if at least one row was updated in any table
    if ($affected1 > 0 || $affected2 > 0 || $affected3 > 0) {
        $conn->commit();
        echo json_encode([
            'success' => true,
            'updated' => [
                'emp_info' => $affected1,
                'emp_position' => $affected2,
                'pass_key' => $affected3
            ]
        ]);
    } else {
        $conn->rollback();
        echo json_encode([
            'success' => false,
            'error' => 'No rows updated. Data may be unchanged or EMP_ID not found.',
            'debug' => [
                'emp_id' => $EMP_ID,
                'first_name' => $FIRST_NAME,
                'last_name' => $LAST_NAME,
                'status' => $STATUS,
                'position' => $POSITION,
                'pin_code' => $PIN_CODE
            ]
        ]);
    }
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => 'Update failed: ' . $e->getMessage()]);
}

$conn->close();
?>