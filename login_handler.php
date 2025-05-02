<?php
session_start();
require_once 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM ADMIN WHERE USERNAME = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['PASSWORD'])) {
        $_SESSION['user_id'] = $user['ID'];
        header('Location: index.php');
        exit();
    } else {
        // Redirect back to login form with error flag
        header('Location: login.php?error=1');
        exit();
    }
}
