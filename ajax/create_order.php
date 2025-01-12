<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    echo json_encode(['success' => false, 'message' => 'Необходима авторизация или корзина пуста']);
    exit;
}

try {
    // Проверяем заполнены ли данные пользователя
    $stmt = $db->prepare("SELECT full_name, phone, address FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Проверяем обязательные поля
    if (empty($user['full_name']) || empty($user['phone']) || empty($user['address'])) {
        echo json_encode([
            'success' => false, 
            'message' => 'Для оформления заказа необходимо заполнить все данные в личном кабинете',
            'redirect' => 'profile.php'
        ]);
        exit;
    }

    $db->beginTransaction();

    // Получаем товары из корзины
    $cart_items = $_SESSION['cart'];
    $placeholders = str_repeat('?,', count($cart_items) - 1) . '?';
    $stmt = $db->prepare("SELECT id, price FROM products WHERE id IN ($placeholders)");
    $stmt->execute(array_keys($cart_items));
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Считаем общую сумму
    $total_amount = 0;
    foreach ($products as $product) {
        $total_amount += $product['price'] * $cart_items[$product['id']];
    }

    // Создаем заказ
    $stmt = $db->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'new')");
    $stmt->execute([$_SESSION['user_id'], $total_amount]);
    $order_id = $db->lastInsertId();

    // Добавляем товары в заказ
    $stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($products as $product) {
        $stmt->execute([
            $order_id,
            $product['id'],
            $cart_items[$product['id']],
            $product['price']
        ]);
    }

    // Очищаем корзину
    $_SESSION['cart'] = [];

    $db->commit();
    echo json_encode(['success' => true, 'order_id' => $order_id]);

} catch (Exception $e) {
    $db->rollBack();
    echo json_encode(['success' => false, 'message' => 'Ошибка при создании заказа']);
} 