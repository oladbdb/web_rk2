<?php
$page = 'purchase_history';
$additional_css = '<link rel="stylesheet" href="cart.css" />';
include 'includes/header.php';
include 'includes/db.php';

// Проверяем авторизацию
if (!is_logged_in()) {
    header('Location: index.php');
    exit;
}

// Получаем заказы пользователя
$stmt = $db->prepare("
    SELECT o.*, 
           COUNT(oi.id) as items_count,
           GROUP_CONCAT(p.name || ' (' || oi.quantity || ' шт.)') as products_list
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    WHERE o.user_id = ?
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="purchase-history">
    <h1>История заказов</h1>

    <?php if (empty($orders)): ?>
        <div class="empty-history">
            <p>У вас пока нет заказов</p>
            <a href="shop.php" class="continue-shopping">Перейти к покупкам</a>
        </div>
    <?php else: ?>
        <table class="products-table">
            <thead>
                <tr>
                    <th>№ заказа</th>
                    <th>Дата</th>
                    <th>Состав заказа</th>
                    <th>Количество товаров</th>
                    <th>Сумма</th>
                    <th>Статус</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= $order['id'] ?></td>
                        <td><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></td>
                        <td><?= $order['products_list'] ?></td>
                        <td><?= $order['items_count'] ?> шт.</td>
                        <td><?= number_format($order['total_amount'], 0, ',', ' ') ?> ₽</td>
                        <td>
                            <span class="order-status status-<?= strtolower($order['status']) ?>">
                                <?php
                                $statuses = [
                                    'new' => 'Новый',
                                    'processing' => 'В обработке',
                                    'shipped' => 'Отправлен',
                                    'delivered' => 'Доставлен',
                                    'cancelled' => 'Отменён'
                                ];
                                echo $statuses[$order['status']] ?? $order['status'];
                                ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?> 