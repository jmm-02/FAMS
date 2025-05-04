<?php
session_start();
require_once 'includes/db_connect.php';

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
        echo "<script>alert('Password changed successfully!'); window.location.href = 'index.php';</script>";
    } else {
        echo "<script>alert('Failed to change password!'); window.history.back();</script>";
    }
}
?>