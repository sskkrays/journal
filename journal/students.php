<?php
require_once 'classes/Database.php';
require_once 'auth.php';

// Доступ только для администратора и учителя (студенты не могут менять список)
require_role(['admin', 'teacher']);

$db = new Database();
$error = '';
$success = '';

// Генерация CSRF-токена
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

// Обработка добавления студента
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        die("Ошибка CSRF");
    }

    $fullName = trim($_POST['full_name'] ?? '');
    $groupName = trim($_POST['group_name'] ?? '');

    if ($fullName && $groupName) {
        $db->query("INSERT INTO students (full_name, group_name) VALUES (?, ?)", [$fullName, $groupName]);
        $success = "Студент успешно добавлен!";
    } else {
        $error = "Заполните все поля!";
    }
}

// Обработка удаления студента
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    
    // Сначала удаляем связанные данные (оценки и посещаемость), чтобы не было ошибки внешнего ключа
    $db->query("DELETE FROM grades WHERE student_id = ?", [$deleteId]);
    $db->query("DELETE FROM attendance WHERE student_id = ?", [$deleteId]);
    $db->query("DELETE FROM users WHERE student_id = ?", [$deleteId]); // Удаляем и аккаунт студента, если есть
    
    // Теперь удаляем самого студента
    $db->query("DELETE FROM students WHERE id = ?", [$deleteId]);
    
    header("Location: students.php");
    exit;
}

// Получаем список студентов
$students = $db->query("SELECT * FROM students ORDER BY group_name, full_name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление студентами</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f7f6; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h2 { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; }
        .back { display: inline-block; margin-bottom: 20px; color: #3498db; text-decoration: none; font-weight: bold; }
        .back:hover { text-decoration: underline; }
        .form-add { background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #dee2e6; margin-bottom: 30px; display: flex; gap: 10px; flex-wrap: wrap; }
        .form-add input { flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 5px; min-width: 200px; }
        .form-add button { padding: 10px 20px; background: #27ae60; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .form-add button:hover { background: #219150; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #3498db; color: white; }
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
    <h2>Список студентов</h2>

    <?php if($error) echo "<div class='msg-error'>$error</div>"; ?>
    <?php if($success) echo "<div class='msg-success'>$success</div>"; ?>

    <!-- Форма добавления -->
    <form method="POST" class="form-add">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>">
        <input type="text" name="full_name" placeholder="ФИО студента (например, Иванов Иван)" required>
        <input type="text" name="group_name" placeholder="Группа (например, ИСП-34)" required>
        <button type="submit">Добавить студента</button>
    </form>

    <!-- Таблица -->
    <?php if (empty($students)): ?>
        <p style="text-align:center; color:#7f8c8d;">Студентов пока нет.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ФИО</th>
                    <th>Группа</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($students as $s): ?>
                <tr>
                    <td><?php echo $s['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($s['full_name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($s['group_name']); ?></td>
                    <td>
                        <a href="students.php?delete=<?php echo $s['id']; ?>" class="btn-del" onclick="return confirm('Удалить студента и все его оценки?')">Удалить</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>