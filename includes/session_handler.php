<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Regenerate session ID to prevent session fixation attacks
if (!isset($_SESSION['CREATED'])) {
    $_SESSION['CREATED'] = time();
} elseif (time() - $_SESSION['CREATED'] > 1800) { // 30 minutes
    session_regenerate_id(true);
    $_SESSION['CREATED'] = time();
}

// Check if the user is logged in
if (!isset($_SESSION['USERNAME'])) {
    header('Location: login.php');
    exit();
}
?>