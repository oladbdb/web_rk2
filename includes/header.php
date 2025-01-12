<?php include_once 'auth.php'; ?>
<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Sneaker Step</title>
    <meta name="description" content="Веб-технологии Байкова Мария 231-3212" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jura:wght@300..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css" />
    
    <?php if (isset($additional_css)) echo $additional_css; ?>
    <script src="scripts/auth.js" defer></script>
  </head>
  <body>
    <header>
      <h1>Sneaker Step</h1>
      <nav>
        <div class="nav-left">
          <a href="index.php"><img class="logo" src="img/logo.svg" alt="Логотип" /></a>
          <a href="index.php#section-1" <?php echo $page == 'home' ? 'class="active"' : ''; ?>>Главная</a>
          <a href="shop.php" <?php echo $page == 'shop' ? 'class="active"' : ''; ?>>Магазин</a>
          <a href="cart.php" class="cart-link">
              Корзина
              <?php
              $cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
              if ($cart_count > 0):
              ?>
                  <span class="cart-counter"><?= $cart_count ?></span>
              <?php endif; ?>
          </a>
          <a href="#footer">Контакты</a>
          <?php if (!is_logged_in()): ?>
            <div class="auth-buttons">
                <a href="#" onclick="showLoginForm(event)">Войти</a>
                <a href="#" onclick="showRegisterForm(event)">Регистрация</a>
            </div>
            
            <div id="loginForm" class="header-form">
                <form action="login.php" method="post">
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Пароль" required>
                    <button type="submit">Войти</button>
                    <a href="#" onclick="hideLoginForm(event)" class="cancel-btn">Отмена</a>
                </form>
            </div>

            <div id="registerForm" class="header-form">
                <form action="register.php" method="post">
                    <input type="text" name="username" placeholder="Имя пользователя" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Пароль" required>
                    <button type="submit">Зарегистрироваться</button>
                    <a href="#" onclick="hideRegisterForm(event)" class="cancel-btn">Отмена</a>
                </form>
            </div>
          <?php else: ?>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="admin_panel.php">Панель управления</a>
            <?php else: ?>
                <a href="purchase_history.php">История покупок</a>
                <a href="feedback.php">Обратная связь</a>
            <?php endif; ?>
            <a href="profile.php" class="username">Привет, <?= htmlspecialchars($_SESSION['username']) ?>!</a>
            <form action="logout.php" method="post" style="display: inline;">
              <button type="submit">Выйти</button>
            </form>
          <?php endif; ?>
        </div>
      </nav>
    </header>
  </body>
</html> 