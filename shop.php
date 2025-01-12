<?php
$page = 'shop';
$additional_css = '<link rel="stylesheet" href="shop.css" />';
include 'includes/header.php';
include 'includes/db.php';

// Получаем бренды
$brands_query = $db->query("SELECT * FROM brands");
$brands = $brands_query->fetchAll(PDO::FETCH_ASSOC);

// Получаем товары по категориям
$male_products = $db->query("SELECT p.*, b.name as brand_name 
                            FROM products p 
                            JOIN brands b ON p.brand_id = b.id 
                            WHERE p.category_id = 1")->fetchAll(PDO::FETCH_ASSOC);

$female_products = $db->query("SELECT p.*, b.name as brand_name 
                              FROM products p 
                              JOIN brands b ON p.brand_id = b.id 
                              WHERE p.category_id = 2")->fetchAll(PDO::FETCH_ASSOC);

$accessories = $db->query("SELECT p.*, b.name as brand_name 
                          FROM products p 
                          JOIN brands b ON p.brand_id = b.id 
                          WHERE p.category_id = 3")->fetchAll(PDO::FETCH_ASSOC);

$view_mode = isset($_GET['view']) && $_GET['view'] === 'table' ? 'table' : 'grid';
?>

<main>
    <div class="view-controls">
        <a href="?view=grid" class="view-mode <?= $view_mode === 'grid' ? 'active' : '' ?>">
            <i class="fas fa-th"></i> Сетка
        </a>
        <a href="?view=table" class="view-mode <?= $view_mode === 'table' ? 'active' : '' ?>">
            <i class="fas fa-list"></i> Таблица
        </a>
    </div>

    <section id="male">
        <h2>Мужская обувь</h2>
        
        <?php if ($view_mode === 'grid'): ?>
            <div class="all_food" id="soups-list">
                <?php foreach($male_products as $product): ?>
                    <a href="product.php?id=<?= $product['id'] ?>" class="view-details">
                        <div class="product-card" data-brand="<?= strtolower($product['brand_name']) ?>">
                            <div class="product-image">
                                <img src="<?= $product['image_url'] ?>" alt="<?= $product['name'] ?>">
                            </div>
                            <p class='name'><?= $product['name'] ?></p>
                            <p class="price">
                                <?= isset($product['price']) ? number_format((float)$product['price'], 0, ',', ' ') : 0 ?> ₽
                            </p>
                            <button class="add-to-cart" data-id="<?= $product['id'] ?>">В корзину</button>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <table class="products-table">
                <thead>
                    <tr>
                        <th>Фото</th>
                        <th>Название</th>
                        <th>Бренд</th>
                        <th>Описание</th>
                        <th>Цена</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($male_products as $product): ?>
                        <tr data-brand="<?= strtolower($product['brand_name']) ?>">
                            <td><img src="<?= $product['image_url'] ?>" alt="<?= $product['name'] ?>" class="table-image"></td>
                            <td><?= $product['name'] ?></td>
                            <td><?= $product['brand_name'] ?></td>
                            <td><?= mb_substr($product['description'], 0, 100) ?>...</td>
                            <td><?= isset($product['price']) ? number_format((float)$product['price'], 0, ',', ' ') : 0 ?> ₽</td>
                            <td>
                                <a href="product.php?id=<?= $product['id'] ?>" class="view-details-btn">Подробнее</a>
                                <button class="add-to-cart" data-id="<?= $product['id'] ?>">В корзину</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>

    <section id="female">
        <h2>Женская обувь</h2>
        <?php if ($view_mode === 'grid'): ?>
            <div class="all_food" id="soups-list">
                <?php foreach($female_products as $product): ?>
                    <a href="product.php?id=<?= $product['id'] ?>" class="view-details">
                        <div class="product-card" data-brand="<?= strtolower($product['brand_name']) ?>">
                            <div class="product-image">
                                <img src="<?= $product['image_url'] ?>" alt="<?= $product['name'] ?>">
                            </div>
                            <p class='name'><?= $product['name'] ?></p>
                            <p class="price">
                                <?= isset($product['price']) ? number_format((float)$product['price'], 0, ',', ' ') : 0 ?> ₽
                            </p>
                            <button class="add-to-cart" data-id="<?= $product['id'] ?>">В корзину</button>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <table class="products-table">
                <thead>
                    <tr>
                        <th>Фото</th>
                        <th>Название</th>
                        <th>Бренд</th>
                        <th>Описание</th>
                        <th>Цена</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($female_products as $product): ?>
                        <tr data-brand="<?= strtolower($product['brand_name']) ?>">
                            <td><img src="<?= $product['image_url'] ?>" alt="<?= $product['name'] ?>" class="table-image"></td>
                            <td><?= $product['name'] ?></td>
                            <td><?= $product['brand_name'] ?></td>
                            <td><?= mb_substr($product['description'], 0, 100) ?>...</td>
                            <td><?= isset($product['price']) ? number_format((float)$product['price'], 0, ',', ' ') : 0 ?> ₽</td>
                            <td>
                                <a href="product.php?id=<?= $product['id'] ?>" class="view-details-btn">Подробнее</a>
                                <button class="add-to-cart" data-id="<?= $product['id'] ?>">В корзину</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>

    <section id="accessories">
        <h2>Аксессуары</h2>
        <?php if ($view_mode === 'grid'): ?>
            <div class="all_food" id="soups-list">
                <?php foreach($accessories as $product): ?>
                    <a href="product.php?id=<?= $product['id'] ?>" class="view-details">
                        <div class="product-card" data-brand="<?= strtolower($product['brand_name']) ?>">
                            <div class="product-image">
                                <img src="<?= $product['image_url'] ?>" alt="<?= $product['name'] ?>">
                            </div>
                            <p class='name'><?= $product['name'] ?></p>
                            <p class="price">
                                <?= isset($product['price']) ? number_format((float)$product['price'], 0, ',', ' ') : 0 ?> ₽
                            </p>
                            <button class="add-to-cart" data-id="<?= $product['id'] ?>">В корзину</button>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <table class="products-table">
                <thead>
                    <tr>
                        <th>Фото</th>
                        <th>Название</th>
                        <th>Бренд</th>
                        <th>Описание</th>
                        <th>Цена</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($accessories as $product): ?>
                        <tr data-brand="<?= strtolower($product['brand_name']) ?>">
                            <td><img src="<?= $product['image_url'] ?>" alt="<?= $product['name'] ?>" class="table-image"></td>
                            <td><?= $product['name'] ?></td>
                            <td><?= $product['brand_name'] ?></td>
                            <td><?= mb_substr($product['description'], 0, 100) ?>...</td>
                            <td><?= isset($product['price']) ? number_format((float)$product['price'], 0, ',', ' ') : 0 ?> ₽</td>
                            <td>
                                <a href="product.php?id=<?= $product['id'] ?>" class="view-details-btn">Подробнее</a>
                                <button class="add-to-cart" data-id="<?= $product['id'] ?>">В корзину</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</main>

<script src="scripts/list-item.js"></script>
<script src="scripts/filters.js"></script>
<script src="scripts/cart.js"></script>

<?php include 'includes/footer.php'; ?> 