<?php
include 'includes/db.php';
include 'includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (register_user($username, $email, $password)) {
        login_user($email, $password);
        header('Location: index.php');
    } else {
        header('Location: index.php?error=registration_failed');
    }
    exit;
}
?> 