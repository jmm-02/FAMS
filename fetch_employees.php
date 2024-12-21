<?php
// Database connection
$host = 'localhost';
$dbname = 'attendance_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Join query to get all employee information
    $query = "SELECT 
        ei.ID as emp_id,
        ei.FIRST_NAME,
        ei.LAST_NAME,
        ei.STATUS,
        ep.POSITION,
        pk.PIN_CODE
    FROM EMP_INFO ei
    LEFT JOIN EMP_POSITION ep ON ei.ID = ep.EMP_ID
    LEFT JOIN PASS_KEY pk ON ei.ID = pk.EMP_ID";

    $stmt = $pdo->query($query);
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($employees);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?> 