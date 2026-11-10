<?php
require_once 'classes/Database.php';
require_once 'auth.php';
require_role(['admin', 'teacher']);

$db = new Database();
$students = $db->query("SELECT id, full_name FROM students")->fetchAll();
$subjects = $db->query("SELECT id, title FROM subjects")->fetchAll();
$error = '';
$success = '';

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        die("Ошибка CSRF");
    }

    $studentId = (int)($_POST['student_id'] ?? 0);
    $subjectId = (int)($_POST['subject_id'] ?? 0);
    $grade = (int)($_POST['grade'] ?? 0);
    $date = $_POST['date'] ?? date('Y-m-d');

    if ($studentId && $subjectId && $grade >= 2 && $grade <= 5) {
        $db->query("INSERT INTO grades (student_id, subject_id, grade, date) VALUES (?, ?, ?, ?)", [$studentId, $subjectId, $grade, $date]);
        $db->query("INSERT INTO logs (user_login, action, created_at) VALUES (?, ?, ?)", 
                   [$_SESSION['user']['login'], "Добавил оценку $grade студенту ID $studentId", date('Y-m-d H:i:s')]);
        $success = "Оценка успешно добавлена!";
    } else {
        $error = "Проверьте данные. Оценка должна быть от 2 до 5.";
    }
}
?>
<!DOCTYPE html>
<html lang="ru"><head><meta charset="UTF-8"><title>Добавить оценку</title>
<style>body{font-family:Arial;background:#f4f7f6;padding:20px}.container{max-width:400px;margin:0 auto;background:#fff;padding:30px;border-radius:10px;box-shadow:0 4px 15px rgba(0,0,0,0.1)}input,select{width:100%;padding:10px;margin-bottom:15px;box-sizing:border-box}button{width:100%;padding:10px;background:#27ae60;color:white;border:none;border-radius:5px;cursor:pointer}.back{display:block;text-align:center;margin-top:15px;color:#3498db;text-decoration:none}.error{color:red}.success{color:green}</style></head><body>
<div class="container">
    <h2>Добавить оценку</h2>
    <?php if($error) echo "<p class='error'>$error</p>"; ?>
    <?php if($success) echo "<p class='success'>$success</p>"; ?>
    <form method="POST">
        <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>">
        <label>Студент:</label>
        <select name="student_id" required><?php foreach($students as $s) echo "<option value='{$s['id']}'>{$s['full_name']}</option>"; ?></select>
        <label>Предмет:</label>
        <select name="subject_id" required><?php foreach($subjects as $sub) echo "<option value='{$sub['id']}'>{$sub['title']}</option>"; ?></select>
        <label>Оценка (2-5):</label>
        <input type="number" name="grade" min="2" max="5" required>
        <label>Дата:</label>
        <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
        <button type="submit">Добавить</button>
    </form>
    <a href="index.php" class="back">← На главную</a>
</div>
</body></html>