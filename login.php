<?php
session_start();

// Записываем имя в сессию (на русском для вывода на экран)
$_SESSION['username'] = "Ниёле";

// Создаем cookie сроком на 1 час (на латинице для надежности)
setcookie("username", "Niele", time() + 3600, "/");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f7f6; text-align: center; padding-top: 50px; }
        .box { background: white; display: inline-block; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h2 { color: #27ae60; }
        a { display: inline-block; margin-top: 15px; padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; }
        a:hover { background: #2980b9; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Вы вошли как: <?php echo $_SESSION['username']; ?></h2>
        <p>Данные сессии и Cookie успешно созданы.</p>
        <a href="welcome.php">Перейти на страницу Welcome</a>
    </div>
</body>
</html>