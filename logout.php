<?php
// 1. Запускаем сессию
session_start();

// 2. Очищаем все переменные сессии
$_SESSION = array();

// 3. Уничтожаем сессию на сервере
session_unset();
session_destroy();

// 4. Удаляем cookie (устанавливаем время жизни в прошлое)
setcookie("username", "", time() - 3600, "/");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Выход</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f7f6; text-align: center; padding-top: 50px; }
        .box { background: white; display: inline-block; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h2 { color: #e74c3c; }
        a { display: inline-block; margin-top: 15px; padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Вы вышли из системы</h2>
        <p>Сессия уничтожена, Cookie удалено.</p>
        <a href="login.php">Вернуться на страницу входа</a>
    </div>
</body>
</html>