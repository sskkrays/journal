<?php
require_once 'classes/Database.php';
require_once 'classes/User.php';
session_start();

if (isset($_SESSION['user'])) { header('Location: index.php'); exit; }
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($login && $password) {
        $db = new Database();
        $user = User::findByLogin($login, $db);
        if ($user) {
            $stmt = $db->query("SELECT password_hash FROM users WHERE id = ?", [$user->id]);
            $hash = $stmt->fetchColumn();
            if (password_verify($password, $hash)) {
                $_SESSION['user'] = ['id' => $user->id, 'login' => $user->login, 'role' => $user->role, 'student_id' => $user->studentId];
                header('Location: index.php'); exit;
            } else { $error = "Неверный логин или пароль"; }
        } else { $error = "Неверный логин или пароль"; }
    } else { $error = "Заполните все поля"; }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход в систему</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f7f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .box { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 300px; }
        h2 { text-align: center; color: #2c3e50; }
        input { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; }
        button:hover { background: #2980b9; }
        .error { color: #e74c3c; text-align: center; margin-bottom: 10px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Вход</h2>
        <?php if ($error): ?><div class="error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <form method="POST">
            <input type="text" name="login" placeholder="Логин" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <button type="submit">Войти</button>
        </form>
        <p style="text-align: center; margin-top: 15px;"><a href="forgot_password.php" style="color: #7f8c8d; font-size: 14px; text-decoration: none;">Забыли пароль?</a></p>
    </div>
</body>
</html>