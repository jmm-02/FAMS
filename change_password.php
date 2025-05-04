<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/session_handler.php';

// Check if the user is logged in
if (!isset($_SESSION['USERNAME'])) {
    echo "<script>alert('User not logged in!'); window.location.href = 'login.php';</script>";
    exit;
}

// Regenerate session ID to prevent session fixation attacks
if (!isset($_SESSION['CREATED'])) {
    $_SESSION['CREATED'] = time();
} elseif (time() - $_SESSION['CREATED'] > 1800) { // 30 minutes
    // Regenerate session ID every 30 minutes
    session_regenerate_id(true);
    $_SESSION['CREATED'] = time();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        echo "<script>alert('New passwords do not match!'); window.history.back();</script>";
        exit;
    }

    $USERNAME = $_SESSION['USERNAME']; // Assuming the username is stored in the session
    $query = "SELECT PASSWORD FROM admin WHERE USERNAME = :username";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':username', $USERNAME);
    $stmt->execute();
    $hashedPassword = $stmt->fetchColumn();

    if (!$hashedPassword || !password_verify($currentPassword, $hashedPassword)) {
        echo "<script>alert('Current password is incorrect!'); window.history.back();</script>";
        exit;
    }

    $newHashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
    $updateQuery = "UPDATE admin SET PASSWORD = :new_password WHERE USERNAME = :username";
    $updateStmt = $pdo->prepare($updateQuery);
    $updateStmt->bindValue(':new_password', $newHashedPassword);
    $updateStmt->bindValue(':username', $USERNAME);

    if ($updateStmt->execute()) {
        echo "<script>alert('Password changed successfully!'); window.location.href = 'login.php';</script>";
    } else {
        echo "<script>alert('Failed to change password!'); window.history.back();</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .change-password-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            width: 400px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #006633;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #004d00;
        }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
    <div class="change-password-container">
        <h3>Change Password</h3>
        <form method="POST" action="change_password_handler.php">
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" placeholder="Enter Current Password" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" placeholder="Enter New Password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm New Password" required>
            </div>
            <button type="submit">Change Password</button>
        </form>
    </div>
</body>
</html>