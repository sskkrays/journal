<?php
session_start();

// Проверка авторизации через СЕССИЮ
if (!isset($_SESSION['username'])) {
    die("<h2 style='text-align:center; color:red; font-family:Arial;'>Вы не авторизованы. <a href='login.php'>Войти</a></h2>");
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добро пожаловать</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f7f6; text-align: center; padding-top: 50px; }
        .box { background: white; display: inline-block; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); text-align: left; }
        .session-info { background: #e8f8f5; padding: 15px; border-left: 5px solid #1abc9c; margin-bottom: 15px; }
        .cookie-info { background: #fef9e7; padding: 15px; border-left: 5px solid #f1c40f; margin-bottom: 15px; }
        .nav { text-align: center; margin-top: 20px; }
        a.btn { display: inline-block; margin: 5px; padding: 10px 15px; color: white; text-decoration: none; border-radius: 5px; }
        .btn-home { background: #3498db; }
        .btn-logout { background: #e74c3c; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Добро пожаловать, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        
        <div class="session-info">
            <strong>Данные из СЕССИИ ($_SESSION):</strong><br>
            Пользователь: <?php echo htmlspecialchars($_SESSION['username']); ?><br>
            <small><i>(Хранится на сервере)</i></small>
        </div>

        <div class="cookie-info">
            <strong>Данные из COOKIE ($_COOKIE):</strong><br>
            <?php 
            // Проверяем наличие cookie через isset
            if (isset($_COOKIE['username'])) {
                echo "Имя в cookie: " . htmlspecialchars($_COOKIE['username']);
            } else {
                echo "Cookie с именем 'username' не найдено.";
            }
            ?>
            <br><small><i>(Хранится в браузере пользователя)</i></small>
        </div>

        <div class="nav">
            <a href="welcome.php" class="btn btn-home">Обновить страницу</a>
            <a href="logout.php" class="btn btn-logout">Выйти</a>
        </div>
    </div>
</body>
</html>