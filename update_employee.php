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
$NAME       = isset($_POST['name']) ? trim($_POST['name']) : '';
$DEPARTMENT = isset($_POST['department']) ? trim($_POST['department']) : '';
$STATUS     = isset($_POST['status']) ? trim($_POST['status']) : '';

if (!$EMP_ID || !$NAME || !$DEPARTMENT) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid or missing input',
        'debug' => [
            'emp_id' => $EMP_ID,
            'name' => $NAME,
            'department' => $DEPARTMENT,
            'status' => $STATUS
        ]
    ]);
    exit;
}

$conn->begin_transaction();

try {
    // Update emp_info
    $stmt = $conn->prepare("UPDATE emp_info SET Name=?, DEPT=?, STATUS=? WHERE ID=?");
    $stmt->bind_param("ssss", $NAME, $DEPARTMENT, $STATUS, $EMP_ID);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();

    // Check if at least one row was updated
    if ($affected > 0) {
        $conn->commit();
        echo json_encode([
            'success' => true,
            'updated' => [
                'emp_info' => $affected
            ]
        ]);
    } else {
        $conn->rollback();
        echo json_encode([
            'success' => false,
            'error' => 'No rows updated. Data may be unchanged or EMP_ID not found.',
            'debug' => [
                'emp_id' => $EMP_ID,
                'name' => $NAME,
                'department' => $DEPARTMENT,
                'status' => $STATUS
            ]
        ]);
    }
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => 'Update failed: ' . $e->getMessage()]);
}

$conn->close();
?>