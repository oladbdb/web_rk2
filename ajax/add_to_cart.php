<?php
session_start();

$data = json_decode(file_get_contents('php://input'), true);
$product_id = $data['product_id'];

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Если товар уже есть в корзине, увеличиваем количество на 1
if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id]++;
} else {
    // Если товара нет в корзине, добавляем его с количеством 1
    $_SESSION['cart'][$product_id] = 1;
}

// Возвращаем общее количество товаров в корзине
$total_items = array_sum($_SESSION['cart']);

echo json_encode([
    'success' => true,
    'total_items' => $total_items
]); 