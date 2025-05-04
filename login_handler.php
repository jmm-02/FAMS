<?php
session_start();
require_once 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch user data from the database
    $query = "SELECT PASSWORD FROM admin WHERE USERNAME = :username";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':username', $username);
    $stmt->execute();
    $hashedPassword = $stmt->fetchColumn();

    // Verify the password
    if ($hashedPassword && password_verify($password, $hashedPassword)) {
        $_SESSION['USERNAME'] = $username; // Store username in session
        header('Location: index.php'); // Redirect to the dashboard
        exit;
    } else {
        header('Location: login.php?error=1'); // Redirect back with an error
        exit;
    }
}
?>
