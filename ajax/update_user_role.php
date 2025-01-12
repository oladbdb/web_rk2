<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Доступ запрещен']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $data['user_id'];
$role = $data['role'];

// Проверяем, не пытается ли админ изменить свою роль
if ($user_id == $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'message' => 'Нельзя изменить свою роль']);
    exit;
}

try {
    $stmt = $db->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->execute([$role, $user_id]);
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Ошибка при обновлении роли']);
} 