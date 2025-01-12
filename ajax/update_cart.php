<?php
session_start();

$data = json_decode(file_get_contents('php://input'), true);
$product_id = $data['product_id'];
$quantity = $data['quantity'];

if ($quantity > 0) {
    $_SESSION['cart'][$product_id] = $quantity;
} else {
    unset($_SESSION['cart'][$product_id]);
}

echo json_encode(['success' => true]); 