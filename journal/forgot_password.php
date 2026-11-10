<?php
require_once 'classes/Database.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    
    if ($login) {
        $db = new Database();
        $stmt = $db->query("SELECT id FROM users WHERE login = ?", [$login]);
        $user = $stmt->fetch();
        
        if ($user) {
            $newPassword = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6);
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $db->query("UPDATE users SET password_hash = ? WHERE id = ?", [$hash, $user['id']]);
            $message = "Новый пароль для <strong>$login</strong>: <span style='background:#fff;padding:2px 5px;border:1px solid #ccc;font-family:monospace;'>$newPassword</span><br><small style='color:#7f8c8d;'>(В реальной жизни это письмо ушло бы вам на email)</small>";
        } else {
            $error = "Пользователь с таким логином не найден.";
        }
    } else {
        $error = "Введите логин.";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Восстановление пароля</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f7f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .box { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 350px; }
        h2 { text-align: center; color: #2c3e50; margin-bottom: 20px; }
        input { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; }
        button:hover { background: #2980b9; }
        .msg { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 15px; text-align: center; font-size: 14px; }
        .error { color: #e74c3c; text-align: center; margin-bottom: 10px; font-size: 14px; }
        .back { display: block; text-align: center; margin-top: 15px; color: #3498db; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Сброс пароля</h2>
        <?php if ($message): ?><div class="msg"><?php echo $message; ?></div><?php endif; ?>
        <?php if ($error): ?><div class="error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <form method="POST">
            <input type="text" name="login" placeholder="Введите ваш логин" required>
            <button type="submit">Сгенерировать пароль</button>
        </form>
        <a href="login.php" class="back">Вернуться к входу</a>
    </div>
</body>
</html>