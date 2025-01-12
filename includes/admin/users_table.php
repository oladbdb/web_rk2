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