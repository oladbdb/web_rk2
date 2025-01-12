<?php
$db_path = __DIR__ . '/../database/sneakers.db';
$db_exists = file_exists($db_path);

try {
    $db = new PDO("sqlite:$db_path");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    
    $db->exec("
        CREATE TABLE IF NOT EXISTS brands (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL
        );

        CREATE TABLE IF NOT EXISTS categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL
        );

        CREATE TABLE IF NOT EXISTS products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            description TEXT,
            image_url TEXT,
            brand_id INTEGER,
            category_id INTEGER,
            FOREIGN KEY (brand_id) REFERENCES brands(id),
            FOREIGN KEY (category_id) REFERENCES categories(id)
        );

        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            email TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS purchase_history (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            product_id INTEGER,
            quantity INTEGER,
            purchase_date DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id),
            FOREIGN KEY (product_id) REFERENCES products(id)
        );

        CREATE TABLE IF NOT EXISTS feedback (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            message TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        );
    ");

    
    if (!$db_exists) {
        $db->exec("
            INSERT INTO brands (name) VALUES 
            ('Nike'),
            ('Adidas'),
            ('Puma'),
            ('New Balance'),
            ('Reebok'),
            ('Converse'),
            ('Vans'),
            ('Crocs'),
            ('Asics')
        ");

        $db->exec("
            INSERT INTO categories (name) VALUES 
            ('Мужская обувь'),
            ('Женская обувь'),
            ('Аксессуары')
        ");

        $db->exec("
            INSERT INTO products (name, price, description, image_url, brand_id, category_id) VALUES 
            ('Nike Air Max', 12999.99, 'Классические кроссовки Nike Air Max', 'img/products/nike-air-max.jpg', 1, 1),
            ('Adidas Superstar', 8999.99, 'Культовые кроссовки Adidas', 'img/products/adidas-superstar.jpg', 2, 1),
            ('Puma RS-X', 9999.99, 'Женские кроссовки Puma', 'img/products/puma-rsx.jpg', 3, 2)
        ");
    }
} catch(PDOException $e) {
    echo "Ошибка подключения к БД: " . $e->getMessage();
    die();
}
?> 