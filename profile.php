<?php
$page = 'profile';
$additional_css = '<link rel="stylesheet" href="cart.css" />';
include 'includes/header.php';
include 'includes/db.php';

// Проверяем авторизацию
if (!is_logged_in()) {
    header('Location: index.php');
    exit;
}

// Получаем данные пользователя
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Обработка формы обновления данных
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');
    $current_password = trim($_POST['current_password'] ?? '');

    $errors = [];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Неверный формат email';
    }

    $phone = preg_replace('/[^0-9+]/', '', $phone); 
    if (!preg_match('/^\+7[0-9]{10}$/', $phone)) {
        $errors[] = 'Неверный формат телефона. Используйте формат +7XXXXXXXXXX';
    }

    if ($email !== $user['email']) {
        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $_SESSION['user_id']]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = 'Этот email уже используется другим пользователем';
        }
    }

    if (!empty($new_password) && !password_verify($current_password, $user['password'])) {
        $errors[] = 'Неверный текущий пароль';
    }

    if (empty($errors)) {
        try {
            if (!empty($new_password)) {
                $stmt = $db->prepare("UPDATE users SET username = ?, email = ?, phone = ?, full_name = ?, address = ?, password = ? WHERE id = ?");
                $stmt->execute([
                    $username,
                    $email,
                    $phone,
                    $full_name,
                    $address,
                    password_hash($new_password, PASSWORD_DEFAULT),
                    $_SESSION['user_id']
                ]);
            } else {
                $stmt = $db->prepare("UPDATE users SET username = ?, email = ?, phone = ?, full_name = ?, address = ? WHERE id = ?");
                $stmt->execute([$username, $email, $phone, $full_name, $address, $_SESSION['user_id']]);
            }
            
            $_SESSION['username'] = $username;
            $success_message = 'Данные успешно обновлены';
            
            $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $errors[] = 'Ошибка при обновлении данных';
        }
    }
}
?>

<main class="profile-container">
    <h1>Личный кабинет</h1>

    <?php if (!empty($errors)): ?>
        <?php foreach ($errors as $error): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success_message) ?>
        </div>
    <?php endif; ?>

    <div class="profile-section">
        <h2>Личные данные</h2>
        <form method="POST" action="" class="profile-form">
            <div class="form-group">
                <label for="full_name">ФИО:</label>
                <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="username">Имя пользователя:</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="<?= htmlspecialchars($user['email']) ?>" 
                       pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                       title="Введите корректный email адрес"
                       required>
            </div>

            <div class="form-group">
                <label for="phone">Телефон:</label>
                <input type="tel" 
                       id="phone" 
                       name="phone" 
                       value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                       placeholder="+7 (9XX) XXX-XX-XX"
                       required>
            </div>

            <div class="form-group">
                <label for="address">Адрес доставки:</label>
                <textarea id="address" name="address" rows="3" required><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="new_password">Новый пароль (оставьте пустым, чтобы не менять):</label>
                <input type="password" id="new_password" name="new_password" minlength="6">
            </div>

            <div class="form-group">
                <label for="current_password">Текущий пароль (необходим для изменения пароля):</label>
                <input type="password" id="current_password" name="current_password">
            </div>

            <button type="submit" class="submit-button">Сохранить изменения</button>
        </form>
    </div>

    <div class="profile-section">
        <h2>Статистика</h2>
        <div class="stats-grid">
            <?php
            $stmt = $db->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $orders_count = $stmt->fetchColumn();

            $stmt = $db->prepare("SELECT SUM(total_amount) FROM orders WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $total_spent = $stmt->fetchColumn() ?: 0;

            $stmt = $db->prepare("SELECT COUNT(*) FROM feedback WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $feedback_count = $stmt->fetchColumn();
            ?>
            
            <div class="stat-item">
                <span class="stat-label">Заказов:</span>
                <span class="stat-value"><?= $orders_count ?></span>
            </div>
            
            <div class="stat-item">
                <span class="stat-label">Потрачено:</span>
                <span class="stat-value"><?= number_format($total_spent, 0, ',', ' ') ?> ₽</span>
            </div>
            
            <div class="stat-item">
                <span class="stat-label">Отзывов:</span>
                <span class="stat-value"><?= $feedback_count ?></span>
            </div>
        </div>
    </div>

    <div class="profile-links">
        <a href="purchase_history.php" class="profile-link">История заказов</a>
        <a href="feedback.php" class="profile-link">Мои отзывы</a>
    </div>
</main>

<script>
document.getElementById('phone').addEventListener('input', function(e) {
    let x = e.target.value.replace(/\D/g, '')
                         .match(/(\d{0,1})(\d{0,3})(\d{0,3})(\d{0,2})(\d{0,2})/);
    
    if (!x[1] && x[0]) {
        e.target.value = '+7' + x[0];
        return;
    }
    
    if (x[1] === '8') {
        x[1] = '7';
    }
    
    e.target.value = !x[2] ? '+' + x[1] : '+' + x[1] + ' (' + x[2] +
                    (x[3] ? ') ' + x[3] : '') +
                    (x[4] ? '-' + x[4] : '') +
                    (x[5] ? '-' + x[5] : '');
});

document.querySelector('.profile-form').addEventListener('submit', function(e) {
    const phoneInput = document.getElementById('phone');
    const phoneValue = phoneInput.value.replace(/[^0-9+]/g, '');
    
    if (!/^\+7[0-9]{10}$/.test(phoneValue)) {
        e.preventDefault();
        alert('Введите корректный номер телефона в формате +7XXXXXXXXXX');
        phoneInput.focus();
    }
});

document.getElementById('email').addEventListener('input', function(e) {
    const email = e.target.value;
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    
    if (email && !emailRegex.test(email)) {
        e.target.setCustomValidity('Введите корректный email адрес');
    } else {
        e.target.setCustomValidity('');
    }
});
</script>

<?php include 'includes/footer.php'; ?> 