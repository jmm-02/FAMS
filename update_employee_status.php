<?php
// Set content type to JSON
header('Content-Type: application/json');

// Include database connection
require_once 'includes/db_connect.php';

// Check if the required parameters are provided
if (!isset($_POST['emp_id']) || !isset($_POST['status'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Missing required parameters (emp_id and status)'
    ]);
    exit;
}

$empId = $_POST['emp_id'];
$status = $_POST['status'];

// Validate status (should be either 'Active' or 'Inactive')
if ($status !== 'Active' && $status !== 'Inactive') {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid status value. Must be either "Active" or "Inactive"'
    ]);
    exit;
}

try {
    // Update the employee status in the database
    $stmt = $pdo->prepare("UPDATE emp_info SET STATUS = ? WHERE ID = ?");
    $result = $stmt->execute([$status, $empId]);
    
    if ($result && $stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => "Employee status updated to $status"
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to update employee status. Employee ID may not exist.'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
