<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
include '../includes/db_connect.php';

// Get the POST data
$data = json_decode(file_get_contents('php://input'), true);

// Log the received data
error_log("Received data for holiday management: " . print_r($data, true));

if (!isset($data['action'])) {
    echo json_encode(['success' => false, 'error' => 'Missing action parameter']);
    exit;
}

$action = $data['action'];

try {
    // Add a new holiday
    if ($action === 'add') {
        if (!isset($data['date']) || !isset($data['description'])) {
            echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
            exit;
        }
        $date = $data['date'];
        $description = $data['description'];
        
        // Insert into holidays table
        $stmtHoliday = $pdo->prepare("INSERT INTO holidays (DATE, DESCRIPTION) VALUES (:date, :description) 
                                    ON DUPLICATE KEY UPDATE DESCRIPTION = :description");
        $stmtHoliday->execute([':date' => $date, ':description' => $description]);
        
        // Update all emp_rec entries for this date
        $stmtEmpRec = $pdo->prepare("UPDATE emp_rec SET HOLIDAY = 1 WHERE DATE = :date");
        $stmtEmpRec->execute([':date' => $date]);
        
        echo json_encode(['success' => true, 'message' => 'Holiday added successfully']);
    }
    
    // Remove a holiday
    else if ($action === 'remove') {
        if (!isset($data['date'])) {
            echo json_encode(['success' => false, 'error' => 'Missing date parameter']);
            exit;
        }
        $date = $data['date'];
        
        // Delete from holidays table
        $stmtHoliday = $pdo->prepare("DELETE FROM holidays WHERE DATE = :date");
        $stmtHoliday->execute([':date' => $date]);
        
        // Update all emp_rec entries for this date
        $stmtEmpRec = $pdo->prepare("UPDATE emp_rec SET HOLIDAY = 0 WHERE DATE = :date");
        $stmtEmpRec->execute([':date' => $date]);
        
        echo json_encode(['success' => true, 'message' => 'Holiday removed successfully']);
    }
    
    // Get all holidays
    else if ($action === 'get_all') {
        $stmt = $pdo->prepare("SELECT * FROM holidays ORDER BY DATE DESC");
        $stmt->execute();
        $holidays = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'holidays' => $holidays]);
    }
    
    // Check if a date is a holiday
    else if ($action === 'check') {
        if (!isset($data['date'])) {
            echo json_encode(['success' => false, 'error' => 'Missing date parameter']);
            exit;
        }
        $date = $data['date'];
        
        $stmt = $pdo->prepare("SELECT * FROM holidays WHERE DATE = :date");
        $stmt->execute([':date' => $date]);
        $holiday = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($holiday) {
            echo json_encode(['success' => true, 'is_holiday' => true, 'description' => $holiday['DESCRIPTION']]);
        } else {
            echo json_encode(['success' => true, 'is_holiday' => false]);
        }
    }
    
    else {
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    error_log("Exception occurred in holiday management: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?> 