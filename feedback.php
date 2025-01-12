<?php
$page = 'feedback';
$additional_css = '<link rel="stylesheet" href="cart.css" />';
include 'includes/header.php';
include 'includes/db.php';

// Проверяем авторизацию
if (!is_logged_in()) {
    header('Location: index.php');
    exit;
}

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? '');
    
    if (!empty($message)) {
        try {
            $stmt = $db->prepare("INSERT INTO feedback (user_id, message, status) VALUES (?, ?, 'new')");
            $stmt->execute([$_SESSION['user_id'], $message]);
            $success_message = 'Ваш отзыв успешно отправлен!';
        } catch (Exception $e) {
            $error_message = 'Произошла ошибка при отправке отзыва';
        }
    } else {
        $error_message = 'Пожалуйста, введите текст отзыва';
    }
}

// Получаем историю отзывов пользователя
$stmt = $db->prepare("
    SELECT message, status, created_at 
    FROM feedback 
    WHERE user_id = ? 
    ORDER BY created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="feedback-container">
    <h1>Обратная связь</h1>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success_message) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>

    <div class="feedback-form">
        <h2>Оставить отзыв</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="message">Ваш отзыв:</label>
                <textarea id="message" name="message" rows="5" required></textarea>
            </div>
            <button type="submit" class="submit-button">Отправить</button>
        </form>
    </div>

    <div class="feedback-history">
        <h2>Ваши отзывы</h2>
        <?php if (empty($feedbacks)): ?>
            <p class="no-feedback">У вас пока нет отзывов</p>
        <?php else: ?>
            <div class="feedback-list">
                <?php foreach ($feedbacks as $feedback): ?>
                    <div class="feedback-item">
                        <div class="feedback-content">
                            <?= nl2br(htmlspecialchars($feedback['message'])) ?>
                        </div>
                        <div class="feedback-meta">
                            <span class="feedback-date">
                                <?= date('d.m.Y H:i', strtotime($feedback['created_at'])) ?>
                            </span>
                            <span class="feedback-status status-<?= strtolower($feedback['status']) ?>">
                                <?php
                                $statuses = [
                                    'new' => 'Новый',
                                    'processed' => 'Обработан',
                                    'answered' => 'Отвечен',
                                    'closed' => 'Закрыт'
                                ];
                                echo $statuses[$feedback['status']] ?? $feedback['status'];
                                ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?> 