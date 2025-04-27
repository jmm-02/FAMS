<?php

// Database connection
$host = 'localhost';
$dbname = 'famsattendance';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Join query to get all employee information, including PIN_CODE from pass_key
    $query = "SELECT 
        ei.ID as emp_id,
        ei.FIRST_NAME,
        ei.LAST_NAME,
        ei.STATUS,
        ep.POSITION,
        pk.PIN_CODE
    FROM emp_info ei
    LEFT JOIN emp_position ep ON ei.ID = ep.EMP_ID
    LEFT JOIN pass_key pk ON ei.ID = pk.EMP_ID";

    $stmt = $pdo->query($query);
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($employees);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>