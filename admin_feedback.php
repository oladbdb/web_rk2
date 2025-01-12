<?php
$page = 'admin_feedback';
$additional_css = '<link rel="stylesheet" href="cart.css" />';
include 'includes/header.php';
include 'includes/db.php';

// Проверяем права администратора
if (!is_logged_in() || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Получаем все отзывы
$stmt = $db->prepare("
    SELECT f.*, u.username, u.email
    FROM feedback f
    JOIN users u ON f.user_id = u.id
    ORDER BY f.created_at DESC
");
$stmt->execute();
$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="admin-feedback">
    <h1>Отзывы пользователей</h1>

    <?php if (empty($feedbacks)): ?>
        <div class="empty-feedback">
            <p>Отзывов пока нет</p>
        </div>
    <?php else: ?>
        <table class="products-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Пользователь</th>
                    <th>Email</th>
                    <th>Отзыв</th>
                    <th>Дата</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($feedbacks as $feedback): ?>
                    <tr class="feedback-item" data-id="<?= $feedback['id'] ?>">
                        <td><?= $feedback['id'] ?></td>
                        <td><?= htmlspecialchars($feedback['username']) ?></td>
                        <td><?= htmlspecialchars($feedback['email']) ?></td>
                        <td><?= htmlspecialchars($feedback['message']) ?></td>
                        <td><?= date('d.m.Y H:i', strtotime($feedback['created_at'])) ?></td>
                        <td>
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
                        </td>
                        <td>
                            <select class="status-select" onchange="updateFeedbackStatus(this)">
                                <?php foreach ($statuses as $key => $value): ?>
                                    <option value="<?= $key ?>" <?= $feedback['status'] === $key ? 'selected' : '' ?>>
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
function updateFeedbackStatus(select) {
    const feedbackId = select.closest('.feedback-item').dataset.id;
    const newStatus = select.value;

    fetch('ajax/update_feedback_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            feedback_id: feedbackId,
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const statusSpan = select.closest('tr').querySelector('.feedback-status');
            statusSpan.className = `feedback-status status-${newStatus}`;
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