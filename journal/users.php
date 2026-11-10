<?php
require_once 'classes/Database.php';
require_once 'auth.php';
require_role(['admin']);

$db = new Database();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    try {
        $db->query("INSERT INTO users (login, password_hash, role) VALUES (?, ?, ?)", [$login, $pass, $role]);
        $db->query("INSERT INTO logs (user_login, action, created_at) VALUES (?, ?, ?)", 
                   [$_SESSION['user']['login'], "Создал пользователя $login", date('Y-m-d H:i:s')]);
        header("Location: users.php"); exit;
    } catch (Exception $e) { $error = "Логин уже занят!"; }
}

if (isset($_GET['delete'])) {
    if ((int)$_GET['delete'] !== 1) { 
        $userId = (int)$_GET['delete'];
        $db->query("DELETE FROM users WHERE id = ?", [$userId]); 
        $db->query("INSERT INTO logs (user_login, action, created_at) VALUES (?, ?, ?)", 
                   [$_SESSION['user']['login'], "Удалил пользователя ID $userId", date('Y-m-d H:i:s')]);
    }
    header("Location: users.php"); exit;
}

$users = $db->query("SELECT id, login, role FROM users")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru"><head><meta charset="UTF-8"><title>Пользователи</title>
<style>body{font-family:Arial;background:#f4f7f6;padding:20px}.container{max-width:700px;margin:0 auto;background:#fff;padding:30px;border-radius:10px;box-shadow:0 4px 15px rgba(0,0,0,0.1)}table{width:100%;border-collapse:collapse;margin-top:20px}th,td{padding:10px;border-bottom:1px solid #ddd;text-align:left}th{background:#8e44ad;color:white}input,select{padding:8px;margin-bottom:10px}</style></head><body>
<div class="container">
    <a href="index.php" style="color:#3498db;text-decoration:none;">← На главную</a>
    <h2>Управление пользователями</h2>
    <?php if($error) echo "<p style='color:red'>$error</p>"; ?>
    <form method="POST" style="background:#f9f9f9;padding:15px;">
        <input type="text" name="login" placeholder="Логин" required>
        <input type="password" name="password" placeholder="Пароль" required>
        <select name="role"><option value="student">Студент</option><option value="teacher">Учитель</option><option value="admin">Админ</option></select>
        <button type="submit">Создать</button>
    </form>
    <table>
        <tr><th>ID</th><th>Логин</th><th>Роль</th><th>Действие</th></tr>
        <?php foreach($users as $u): ?>
        <tr><td><?php echo $u['id']; ?></td><td><?php echo htmlspecialchars($u['login']); ?></td><td><?php echo $u['role']; ?></td>
        <td><?php if($u['id'] != 1) echo "<a href='users.php?delete={$u['id']}' style='color:red' onclick=\"return confirm('Удалить?')\">Удалить</a>"; ?></td></tr>
        <?php endforeach; ?>
    </table>
</div>
</body></html>