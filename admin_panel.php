<?php
$page = 'admin_panel';
$additional_css = '<link rel="stylesheet" href="cart.css" />';
include 'includes/header.php';
include 'includes/db.php';

// Проверяем права администратора
if (!is_logged_in() || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$section = $_GET['section'] ?? 'users';

switch ($section) {
    case 'users':
        $stmt = $db->prepare("
            SELECT u.*, 
                   COUNT(DISTINCT o.id) as orders_count,
                   COUNT(DISTINCT f.id) as feedback_count
            FROM users u
            LEFT JOIN orders o ON u.id = o.user_id
            LEFT JOIN feedback f ON u.id = f.user_id
            GROUP BY u.id
            ORDER BY u.id DESC
        ");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        break;

    case 'orders':
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
        break;

    case 'feedback':
        $stmt = $db->prepare("
            SELECT f.*, u.username, u.email
            FROM feedback f
            JOIN users u ON f.user_id = u.id
            ORDER BY f.created_at DESC
        ");
        $stmt->execute();
        $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        break;
}
?>

<main class="admin-panel">
    <h1>Панель управления</h1>

    <div class="admin-menu">
        <a href="?section=users" class="admin-menu-item <?= $section === 'users' ? 'active' : '' ?>">
            Пользователи
        </a>
        <a href="?section=orders" class="admin-menu-item <?= $section === 'orders' ? 'active' : '' ?>">
            Заказы
        </a>
        <a href="?section=feedback" class="admin-menu-item <?= $section === 'feedback' ? 'active' : '' ?>">
            Отзывы
        </a>
    </div>

    <div class="admin-content">
        <?php if ($section === 'users'): ?>
            <h2>Управление пользователями</h2>
            <!-- Таблица пользователей -->
            <?php include 'includes/admin/users_table.php'; ?>

        <?php elseif ($section === 'orders'): ?>
            <h2>Управление заказами</h2>
            <!-- Таблица заказов -->
            <?php include 'includes/admin/orders_table.php'; ?>

        <?php elseif ($section === 'feedback'): ?>
            <h2>Управление отзывами</h2>
            <!-- Таблица отзывов -->
            <?php include 'includes/admin/feedback_table.php'; ?>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?> 