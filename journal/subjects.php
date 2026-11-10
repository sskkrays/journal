<?php
require_once 'classes/Database.php';
require_once 'auth.php';

// Доступ только для администратора и учителя
require_role(['admin', 'teacher']);

$db = new Database();
$error = '';
$success = '';

// Генерация CSRF-токена
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

// Обработка добавления предмета
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        die("Ошибка CSRF");
    }

    $title = trim($_POST['title'] ?? '');

    if ($title) {
        $db->query("INSERT INTO subjects (title) VALUES (?)", [$title]);
        $success = "Предмет успешно добавлен!";
    } else {
        $error = "Введите название предмета!";
    }
}

// Обработка удаления предмета
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    
    // Сначала удаляем связанные оценки и посещаемость
    $db->query("DELETE FROM grades WHERE subject_id = ?", [$deleteId]);
    $db->query("DELETE FROM attendance WHERE subject_id = ?", [$deleteId]);
    
    // Теперь удаляем сам предмет
    $db->query("DELETE FROM subjects WHERE id = ?", [$deleteId]);
    
    header("Location: subjects.php");
    exit;
}

// Получаем список предметов
$subjects = $db->query("SELECT * FROM subjects ORDER BY title")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление предметами</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f7f6; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h2 { color: #2c3e50; border-bottom: 2px solid #e67e22; padding-bottom: 10px; }
        .back { display: inline-block; margin-bottom: 20px; color: #3498db; text-decoration: none; font-weight: bold; }
        .back:hover { text-decoration: underline; }
        .form-add { background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #dee2e6; margin-bottom: 30px; display: flex; gap: 10px; flex-wrap: wrap; }
        .form-add input { flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 5px; min-width: 200px; }
        .form-add button { padding: 10px 20px; background: #e67e22; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .form-add button:hover { background: #d35400; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #e67e22; color: white; }
        tr:hover { background-color: #f1f1f1; }
        .btn-del { color: #e74c3c; text-decoration: none; font-weight: bold; }
        .btn-del:hover { text-decoration: underline; }
        .msg-success { color: #27ae60; background: #d5f5e3; padding: 10px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #27ae60; }
        .msg-error { color: #e74c3c; background: #fadbd8; padding: 10px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #e74c3c; }
    </style>
</head>
<body>
<div class="container">
    <a href="index.php" class="back">← На главную</a>
    <h2>Список предметов</h2>

    <?php if($error) echo "<div class='msg-error'>$error</div>"; ?>
    <?php if($success) echo "<div class='msg-success'>$success</div>"; ?>

    <!-- Форма добавления -->
    <form method="POST" class="form-add">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>">
        <input type="text" name="title" placeholder="Название предмета (например, Web-дизайн)" required>
        <button type="submit">Добавить предмет</button>
    </form>

    <!-- Таблица -->
    <?php if (empty($subjects)): ?>
        <p style="text-align:center; color:#7f8c8d;">Предметов пока нет.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название предмета</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($subjects as $s): ?>
                <tr>
                    <td><?php echo $s['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($s['title']); ?></strong></td>
                    <td>
                        <a href="subjects.php?delete=<?php echo $s['id']; ?>" class="btn-del" onclick="return confirm('Удалить предмет и все оценки по нему?')">Удалить</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>