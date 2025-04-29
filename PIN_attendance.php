<?php
require_once 'includes/db_connect.php';

// Initialize variables
$message = '';
$current_time = date('H:i:s');
$current_date = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['PIN'])) {
    $pin = $_POST['PIN'];
    
    try {
        // 1. Verify the PIN code
        $sql = "SELECT `ID`, `EMP_ID`, `FINGERPRINT`, `PIN_CODE` FROM `pass_key` WHERE `PIN_CODE` = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$pin]);
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            $emp_id = $row['EMP_ID'];
            
            // 2. Get employee info
            $sql_info = "SELECT `ID`, `FIRST_NAME`, `LAST_NAME`, `STATUS` FROM `emp_info` WHERE `ID` = ?";
            $stmt_info = $pdo->prepare($sql_info);
            $stmt_info->execute([$emp_id]);
            
            if ($stmt_info->rowCount() > 0) {
                $employee = $stmt_info->fetch();
                
                // 3. Check if there's already a record for today
                $sql_check = "SELECT `ID`, `EMP_ID`, `AM_IN`, `AM_OUT`, `PM_IN`, `PM_OUT`, `DATE` 
                              FROM `emp_rec` 
                              WHERE `EMP_ID` = ? AND `DATE` = ?";
                $stmt_check = $pdo->prepare($sql_check);
                $stmt_check->execute([$emp_id, $current_date]);
                
                if ($stmt_check->rowCount() > 0) {
                    // Update existing record
                    $attendance = $stmt_check->fetch();
                    
                    // Determine which field to update based on current time and existing data
                    if (empty($attendance['AM_IN'])) {
                        $sql_update = "UPDATE `emp_rec` SET `AM_IN` = ? WHERE `ID` = ?";
                        $message = "Good morning, " . $employee['FIRST_NAME'] . "! AM IN recorded.";
                    } elseif (empty($attendance['AM_OUT']) && strtotime($current_time) > strtotime('12:00:00')) {
                        $sql_update = "UPDATE `emp_rec` SET `AM_OUT` = ? WHERE `ID` = ?";
                        $message = "Good afternoon, " . $employee['FIRST_NAME'] . "! AM OUT recorded.";
                    } elseif (empty($attendance['PM_IN'])) {
                        $sql_update = "UPDATE `emp_rec` SET `PM_IN` = ? WHERE `ID` = ?";
                        $message = "Good afternoon, " . $employee['FIRST_NAME'] . "! PM IN recorded.";
                    } elseif (empty($attendance['PM_OUT'])) {
                        $sql_update = "UPDATE `emp_rec` SET `PM_OUT` = ? WHERE `ID` = ?";
                        $message = "Good evening, " . $employee['FIRST_NAME'] . "! PM OUT recorded.";
                    } else {
                        $message = "All attendance records for today are already completed.";
                    }
                    
                    if (isset($sql_update)) {
                        $stmt_update = $pdo->prepare($sql_update);
                        $stmt_update->execute([$current_time, $attendance['ID']]);
                    }
                } else {
                    // Create new record (AM IN)
                    $sql_insert = "INSERT INTO `emp_rec` (`EMP_ID`, `AM_IN`, `DATE`) VALUES (?, ?, ?)";
                    $stmt_insert = $pdo->prepare($sql_insert);
                    $stmt_insert->execute([$emp_id, $current_time, $current_date]);
                    $message = "Good morning, " . $employee['FIRST_NAME'] . "! AM IN recorded.";
                }
            } else {
                $message = "Employee not found!";
            }
        } else {
            $message = "Invalid PIN code!";
        }
    } catch(PDOException $e) {
        $message = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance IN & OUT</title>
    <link rel="icon" href="assets/logo.png">
    <link rel="stylesheet" href="assets/pincode.css">
</head>
<body>
    <div class="login-container">
        <img src="assets/logo.png" alt="Logo" class="logo">
        <?php if (!empty($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="PIN">PIN</label>
                <input type="number" id="PIN" name="PIN" placeholder="Enter PIN CODE" required>
            </div>
            <button type="submit">Submit</button>
        </form>
        <br>
    </div>
</body>
</html>