<?php
$page = 'cart';
$additional_css = '<link rel="stylesheet" href="cart.css" />';
include 'includes/header.php';
include 'includes/db.php';

// Получаем товары в корзине из сессии
$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total = 0;

// Получаем информацию о товарах из базы данных
$cart_products = [];
if (!empty($cart_items)) {
    $placeholders = str_repeat('?,', count($cart_items) - 1) . '?';
    $stmt = $db->prepare("SELECT p.*, b.name as brand_name 
                         FROM products p 
                         JOIN brands b ON p.brand_id = b.id 
                         WHERE p.id IN ($placeholders)");
    $stmt->execute(array_keys($cart_items));
    $cart_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<main class="cart-container">
    <h1>Корзина</h1>
    
    <?php if (empty($cart_products)): ?>
        <div class="empty-cart">
            <p>Ваша корзина пуста</p>
            <a href="shop.php" class="continue-shopping">Перейти к покупкам</a>
        </div>
    <?php else: ?>
        <div class="cart-content">
            <table class="products-table">
                <thead>
                    <tr>
                        <th>Фото</th>
                        <th>Название</th>
                        <th>Бренд</th>
                        <th>Цена</th>
                        <th>Количество</th>
                        <th>Сумма</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_products as $product): 
                        $quantity = $cart_items[$product['id']];
                        $subtotal = $product['price'] * $quantity;
                        $total += $subtotal;
                    ?>
                        <tr class="cart-item" data-id="<?= $product['id'] ?>">
                            <td>
                                <img src="<?= $product['image_url'] ?>" alt="<?= $product['name'] ?>" class="table-image">
                            </td>
                            <td><?= $product['name'] ?></td>
                            <td><?= $product['brand_name'] ?></td>
                            <td><?= number_format($product['price'], 0, ',', ' ') ?> ₽</td>
                            <td>
                                <div class="quantity-controls">
                                    <button class="decrease-quantity">-</button>
                                    <input type="number" class="quantity-input" value="<?= $quantity ?>" min="1" max="99">
                                    <button class="increase-quantity">+</button>
                                </div>
                            </td>
                            <td class="item-subtotal">
                                <?= number_format($subtotal, 0, ',', ' ') ?> ₽
                            </td>
                            <td>
                                <button class="remove-item" title="Удалить из корзины">
                                    Удалить
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-summary">
                <h2>Итого</h2>
                <div class="summary-details">
                    <div class="summary-row">
                        <span>Товары (<?= array_sum($cart_items) ?> шт.)</span>
                        <span><?= number_format($total, 0, ',', ' ') ?> ₽</span>
                    </div>
                    <div class="summary-row">
                        <span>Доставка</span>
                        <span>Бесплатно</span>
                    </div>
                    <div class="summary-total">
                        <span>К оплате</span>
                        <span><?= number_format($total, 0, ',', ' ') ?> ₽</span>
                    </div>
                </div>
                <?php if (is_logged_in()): ?>
                    <button class="checkout-button">Оформить заказ</button>
                <?php else: ?>
                    <div class="auth-warning">
                        <p>Для оформления заказа необходимо <a href="#" onclick="showLoginForm(event)">войти</a> или <a href="#" onclick="showRegisterForm(event)">зарегистрироваться</a></p>
                    </div>
                <?php endif; ?>
                <a href="shop.php" class="continue-shopping">Продолжить покупки</a>
            </div>
        </div>
    <?php endif; ?>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Обработчик оформления заказа
    document.querySelector('.checkout-button').addEventListener('click', function() {
        if (!confirm('Подтвердите оформление заказа')) {
            return;
        }

        this.disabled = true;
        this.textContent = 'Оформляем заказ...';

        fetch('ajax/create_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Заказ успешно оформлен!');
                window.location.href = 'purchase_history.php';
            } else {
                if (data.redirect) {
                    if (confirm(data.message)) {
                        window.location.href = data.redirect;
                    }
                } else {
                    alert(data.message || 'Произошла ошибка при оформлении заказа');
                }
                this.disabled = false;
                this.textContent = 'Оформить заказ';
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
            alert('Произошла ошибка при оформлении заказа');
            this.disabled = false;
            this.textContent = 'Оформить заказ';
        });
    });

    // Обработчик изменения количества
    function updateQuantity(productId, newQuantity) {
        fetch('ajax/update_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: newQuantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); 
            }
        });
    }

    // Обработчики кнопок + и -
    document.querySelectorAll('.increase-quantity').forEach(button => {
        button.addEventListener('click', function() {
            const item = this.closest('.cart-item');
            const input = item.querySelector('.quantity-input');
            const newValue = parseInt(input.value) + 1;
            if (newValue <= 99) {
                updateQuantity(item.dataset.id, newValue);
            }
        });
    });

    document.querySelectorAll('.decrease-quantity').forEach(button => {
        button.addEventListener('click', function() {
            const item = this.closest('.cart-item');
            const input = item.querySelector('.quantity-input');
            const newValue = parseInt(input.value) - 1;
            if (newValue >= 1) {
                updateQuantity(item.dataset.id, newValue);
            }
        });
    });

    // Обработчик удаления товара
    document.querySelectorAll('.remove-item').forEach(button => {
        button.addEventListener('click', function() {
            const item = this.closest('.cart-item');
            updateQuantity(item.dataset.id, 0);
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?> 