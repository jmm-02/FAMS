<?php

// Database connection
$host = 'localhost';
$dbname = 'famsattendance';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to get all employee information from the updated database schema
    $query = "SELECT 
        ID as emp_id,
        Name,
        DEPT as department,
        STATUS as status
    FROM emp_info";

    $stmt = $pdo->query($query);
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($employees);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>