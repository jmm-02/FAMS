<?php
$conn = new mysqli("localhost", "root", "", "attendance_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start transaction
$conn->begin_transaction();

try {
    // Insert into EMP_INFO
    $stmt1 = $conn->prepare("INSERT INTO EMP_INFO (FIRST_NAME, LAST_NAME, STATUS) VALUES (?, ?, ?)");
    $stmt1->bind_param("sss", $_POST['first_name'], $_POST['last_name'], $_POST['status']);
    $stmt1->execute();
    
    $emp_id = $conn->insert_id; // Get the auto-generated ID
    
    // Insert into EMP_POSITION
    $stmt2 = $conn->prepare("INSERT INTO EMP_POSITION (EMP_ID, POSITION) VALUES (?, ?)");
    $stmt2->bind_param("is", $emp_id, $_POST['position']);
    $stmt2->execute();
    
    // Insert into PASS_KEY
    $stmt3 = $conn->prepare("INSERT INTO PASS_KEY (EMP_ID, PIN_CODE) VALUES (?, ?)");
    $stmt3->bind_param("is", $emp_id, $_POST['pin_code']);
    $stmt3->execute();
    
    // If everything is successful, commit the transaction
    $conn->commit();
    
    header("Location: employeeinfo.html");
    exit();
    
} catch (Exception $e) {
    // If there's an error, rollback the transaction
    $conn->rollback();
    echo "Error: " . $e->getMessage();
}

$conn->close();
?> 