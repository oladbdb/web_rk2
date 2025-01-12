<?php
include 'includes/db.php';
include 'includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (login_user($email, $password)) {
        header('Location: index.php');
    } else {
        header('Location: index.php?error=login_failed');
    }
    exit;
}
?> 