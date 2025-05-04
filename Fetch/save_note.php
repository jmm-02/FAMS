<?php
// filepath: c:\xampp\htdocs\FAMS\FAMS\Fetch\save_note.php

header('Content-Type: application/json');

// Include the database connection
include '../includes/db_connect.php';

// Check the database connection
if (!$pdo) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed.']);
    exit;
}

// Get the JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Debugging: Log the incoming data
error_log("Incoming Data: " . json_encode($data));

// Validate input
if (!isset($data['emp_id'], $data['date'], $data['note'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}

$emp_id = $data['emp_id'];
$date = $data['date'];
$note = $data['note'];

try {
    // Check if a record already exists for the given employee and date
    $query = "SELECT * FROM emp_rec WHERE EMP_ID = :emp_id AND DATE = :date";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['emp_id' => $emp_id, 'date' => $date]);

    if ($stmt->rowCount() > 0) {
        // Update the existing record
        $updateQuery = "UPDATE emp_rec SET NOTE = :note WHERE EMP_ID = :emp_id AND DATE = :date";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->execute(['note' => $note, 'emp_id' => $emp_id, 'date' => $date]);
        echo json_encode(['success' => true, 'message' => 'Note updated successfully']);
    } else {
        // Insert a new record
        $insertQuery = "INSERT INTO emp_rec (EMP_ID, DATE, NOTE) VALUES (:emp_id, :date, :note)";
        $insertStmt = $pdo->prepare($insertQuery);
        $insertStmt->execute(['emp_id' => $emp_id, 'date' => $date, 'note' => $note]);
        echo json_encode(['success' => true, 'message' => 'Note added successfully']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}

?>