<?php
require_once 'includes/db_connect.php';

function getAllEmployees() {
    global $pdo;
    
    try {
        $query = "SELECT 
            ei.ID,
            ei.FIRST_NAME,
            ei.LAST_NAME,
            ei.STATUS,
            ep.POSITION,
            pk.PIN_CODE
        FROM EMP_INFO ei
        LEFT JOIN EMP_POSITION ep ON ei.ID = ep.EMP_ID
        LEFT JOIN PASS_KEY pk ON ei.ID = pk.EMP_ID";

        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        // Log error or handle it appropriately
        error_log("Error fetching employees: " . $e->getMessage());
        return [];
    }
}

function getEmployeeById($id) {
    global $pdo;
    
    try {
        $query = "SELECT 
            ei.ID,
            ei.FIRST_NAME,
            ei.LAST_NAME,
            ei.STATUS,
            ep.POSITION,
            pk.PIN_CODE
        FROM EMP_INFO ei
        LEFT JOIN EMP_POSITION ep ON ei.ID = ep.EMP_ID
        LEFT JOIN PASS_KEY pk ON ei.ID = pk.EMP_ID
        WHERE ei.ID = :id";

        $stmt = $pdo->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Error fetching employee: " . $e->getMessage());
        return null;
    }
}

// Add other employee-related functions as needed
?>