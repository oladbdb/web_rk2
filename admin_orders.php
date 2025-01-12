<?php
$page = 'admin_orders';
$additional_css = '<link rel="stylesheet" href="cart.css" />';
include 'includes/header.php';
include 'includes/db.php';

// Проверяем права администратора
if (!is_logged_in() || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Получаем все заказы с информацией о пользователях
$stmt = $db->prepare("
    SELECT o.*, u.username, u.email,
           GROUP_CONCAT(p.name || ' (' || oi.quantity || ' шт.)') as products_list
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="admin-orders">
    <h1>Управление заказами</h1>

    <?php if (empty($orders)): ?>
        <div class="empty-orders">
            <p>Заказов пока нет</p>
        </div>
    <?php else: ?>
        <table class="products-table">
            <thead>
                <tr>
                    <th>№ заказа</th>
                    <th>Пользователь</th>
                    <th>Email</th>
                    <th>Состав заказа</th>
                    <th>Сумма</th>
                    <th>Дата</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr class="order-item" data-id="<?= $order['id'] ?>">
                        <td><?= $order['id'] ?></td>
                        <td><?= htmlspecialchars($order['username']) ?></td>
                        <td><?= htmlspecialchars($order['email']) ?></td>
                        <td><?= htmlspecialchars($order['products_list']) ?></td>
                        <td><?= number_format($order['total_amount'], 0, ',', ' ') ?> ₽</td>
                        <td><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></td>
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
                        <td>
                            <select class="status-select" onchange="updateOrderStatus(this)">
                                <?php foreach ($statuses as $key => $value): ?>
                                    <option value="<?= $key ?>" <?= $order['status'] === $key ? 'selected' : '' ?>>
                                        <?= $value ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>

<script>
function updateOrderStatus(select) {
    const orderId = select.closest('.order-item').dataset.id;
    const newStatus = select.value;

    fetch('ajax/update_order_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            order_id: orderId,
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const statusSpan = select.closest('tr').querySelector('.order-status');
            statusSpan.className = `order-status status-${newStatus}`;
            statusSpan.textContent = select.options[select.selectedIndex].text;
        } else {
            alert('Ошибка при обновлении статуса');
        }
    })
    .catch(error => {
        console.error('Ошибка:', error);
        alert('Произошла ошибка при обновлении статуса');
    });
}
</script>

<?php include 'includes/footer.php'; ?> 