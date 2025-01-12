<table class="products-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Пользователь</th>
            <th>Email</th>
            <th>Сообщение</th>
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
                <td class="message-cell"><?= nl2br(htmlspecialchars($feedback['message'])) ?></td>
                <td><?= date('d.m.Y H:i', strtotime($feedback['created_at'])) ?></td>
                <td>
                    <span class="feedback-status status-<?= strtolower($feedback['status']) ?>">
                        <?php
                        $feedbackStatuses = [
                            'new' => 'Новый',
                            'processed' => 'Обработан',
                            'answered' => 'Отвечен',
                            'closed' => 'Закрыт'
                        ];
                        echo $feedbackStatuses[$feedback['status']] ?? $feedback['status'];
                        ?>
                    </span>
                </td>
                <td>
                    <select class="status-select" onchange="updateFeedbackStatus(this)">
                        <?php foreach ($feedbackStatuses as $key => $value): ?>
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
            select.value = select.options[1 - select.selectedIndex].value;
        }
    })
    .catch(error => {
        console.error('Ошибка:', error);
        alert('Произошла ошибка при обновлении статуса');
        select.value = select.options[1 - select.selectedIndex].value;
    });
}
</script> 