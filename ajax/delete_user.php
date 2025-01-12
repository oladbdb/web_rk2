<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Доступ запрещен']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $data['user_id'];

// Проверяем, не пытается ли админ удалить себя
if ($user_id == $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'message' => 'Нельзя удалить свой аккаунт']);
    exit;
}

try {
    $db->beginTransaction();

    // Удаляем связанные данные
    $stmt = $db->prepare("DELETE FROM feedback WHERE user_id = ?");
    $stmt->execute([$user_id]);

    $stmt = $db->prepare("DELETE FROM order_items WHERE order_id IN (SELECT id FROM orders WHERE user_id = ?)");
    $stmt->execute([$user_id]);

    $stmt = $db->prepare("DELETE FROM orders WHERE user_id = ?");
    $stmt->execute([$user_id]);

    // Удаляем пользователя
    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);

    $db->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $db->rollBack();
    echo json_encode(['success' => false, 'message' => 'Ошибка при удалении пользователя']);
} 