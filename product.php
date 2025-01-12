<?php
$page = 'shop';
$additional_css = '<link rel="stylesheet" href="shop.css" />';
include 'includes/header.php';
include 'includes/db.php';

// Получаем ID товара из GET-параметра
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Получаем детальную информацию о товаре
$stmt = $db->prepare("
    SELECT p.*, b.name as brand_name, c.name as category_name,
           CASE 
               WHEN c.id = 1 THEN 'male'
               WHEN c.id = 2 THEN 'female'
               ELSE 'accessory'
           END as product_type
    FROM products p 
    JOIN brands b ON p.brand_id = b.id
    JOIN categories c ON p.category_id = c.id
    WHERE p.id = ?
");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "<p>Товар не найден</p>";
    include 'includes/footer.php';
    exit;
}
?>

<main class="product-details">
    <div class="product-container">
        <div class="product-image">
            <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
        </div>
        <div class="product-info">
            <h1><?= htmlspecialchars($product['name']) ?></h1>
            <p class="brand">Бренд: <?= htmlspecialchars($product['brand_name']) ?></p>
            <p class="price"><?= number_format((float)$product['price'], 0, ',', ' ') ?> ₽</p>
            
            <div class="description">
                <h2>Описание</h2>
                <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            </div>

            <?php if ($product['product_type'] !== 'accessory'): ?>
            <div class="characteristics">
                <h2>Характеристики</h2>
                <table>
                    <tr>
                        <th>Размеры в наличии:</th>
                        <td>36, 37, 38, 39, 40, 41, 42, 43, 44</td>
                    </tr>
                    <tr>
                        <th>Материал верха:</th>
                        <td>Натуральная кожа</td>
                    </tr>
                    <tr>
                        <th>Материал подошвы:</th>
                        <td>Резина</td>
                    </tr>
                    <tr>
                        <th>Сезон:</th>
                        <td>Весна-Лето</td>
                    </tr>
                </table>
            </div>
            <?php else: ?>
            <div class="characteristics">
                <h2>Характеристики</h2>
                <table>
                    <tr>
                        <th>Материал:</th>
                        <td>100% хлопок</td>
                    </tr>
                    <tr>
                        <th>Размер:</th>
                        <td>Универсальный</td>
                    </tr>
                </table>
            </div>
            <?php endif; ?>

            <div class="stock">
                <p>В наличии: <span class="stock-count">5</span> шт.</p>
            </div>

            <button class="add-to-cart" data-id="<?= $product['id'] ?>">Добавить в корзину</button>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?> 