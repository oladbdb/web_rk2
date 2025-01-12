<?php
$page = 'admin_users';
$additional_css = '<link rel="stylesheet" href="cart.css" />';
include 'includes/header.php';
include 'includes/db.php';

// Проверяем права администратора
if (!is_logged_in() || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Получаем всех пользователей
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
?>

<main class="admin-users">
    <h1>Управление пользователями</h1>

    <table class="products-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Имя пользователя</th>
                <th>Email</th>
                <th>ФИО</th>
                <th>Телефон</th>
                <th>Роль</th>
                <th>Заказов</th>
                <th>Отзывов</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr class="user-item" data-id="<?= $user['id'] ?>">
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['full_name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($user['phone'] ?? '') ?></td>
                    <td>
                        <select class="role-select" onchange="updateUserRole(this)" <?= $user['id'] == $_SESSION['user_id'] ? 'disabled' : '' ?>>
                            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>Пользователь</option>
                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Администратор</option>
                        </select>
                    </td>
                    <td><?= $user['orders_count'] ?></td>
                    <td><?= $user['feedback_count'] ?></td>
                    <td>
                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                            <button class="delete-user" onclick="deleteUser(<?= $user['id'] ?>)">Удалить</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<script>
function updateUserRole(select) {
    const userId = select.closest('.user-item').dataset.id;
    const newRole = select.value;

    if (!confirm(`Изменить роль пользователя на "${newRole}"?`)) {
        select.value = select.options[1 - select.selectedIndex].value;
        return;
    }

    fetch('ajax/update_user_role.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            user_id: userId,
            role: newRole
        })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            alert(data.message || 'Ошибка при обновлении роли');
            select.value = select.options[1 - select.selectedIndex].value;
        }
    })
    .catch(error => {
        console.error('Ошибка:', error);
        alert('Произошла ошибка при обновлении роли');
        select.value = select.options[1 - select.selectedIndex].value;
    });
}

function deleteUser(userId) {
    if (!confirm('Вы уверены, что хотите удалить этого пользователя? Это действие нельзя отменить.')) {
        return;
    }

    fetch('ajax/delete_user.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            user_id: userId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelector(`.user-item[data-id="${userId}"]`).remove();
        } else {
            alert(data.message || 'Ошибка при удалении пользователя');
        }
    })
    .catch(error => {
        console.error('Ошибка:', error);
        alert('Произошла ошибка при удалении пользователя');
    });
}
</script>

<?php include 'includes/footer.php'; ?> 