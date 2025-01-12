<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Доступ запрещен']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$order_id = $data['order_id'];
$status = $data['status'];

try {
    $stmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $order_id]);
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Ошибка при обновлении статуса']);
} 