<?php
require_once 'classes/Database.php';
require_once 'auth.php';
require_role(['admin', 'teacher']);

$db = new Database();
$students = $db->query("SELECT id, full_name FROM students")->fetchAll();
$subjects = $db->query("SELECT id, title FROM subjects")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db->query("INSERT INTO attendance (student_id, subject_id, date, status) VALUES (?, ?, ?, ?)", [$_POST['student_id'], $_POST['subject_id'], $_POST['date'], $_POST['status']]);
    header("Location: attendance.php"); exit;
}

$logs = $db->query("SELECT a.id, s.full_name, sub.title, a.date, a.status FROM attendance a JOIN students s ON s.id = a.student_id JOIN subjects sub ON sub.id = a.subject_id ORDER BY a.date DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru"><head><meta charset="UTF-8"><title>Посещаемость</title>
<style>body{font-family:Arial;background:#f4f7f6;padding:20px}.container{max-width:800px;margin:0 auto;background:#fff;padding:30px;border-radius:10px;box-shadow:0 4px 15px rgba(0,0,0,0.1)}table{width:100%;border-collapse:collapse;margin-top:20px}th,td{padding:10px;border-bottom:1px solid #ddd;text-align:left}th{background:#e67e22;color:white}input,select{padding:8px;margin-right:10px;margin-bottom:10px}</style></head><body>
<div class="container">
    <a href="index.php" style="color:#3498db;text-decoration:none;">← На главную</a>
    <h2>Посещаемость</h2>
    <form method="POST" style="background:#f9f9f9;padding:15px;border-radius:5px;">
        <select name="student_id"><?php foreach($students as $s) echo "<option value='{$s['id']}'>{$s['full_name']}</option>"; ?></select>
        <select name="subject_id"><?php foreach($subjects as $s) echo "<option value='{$s['id']}'>{$s['title']}</option>"; ?></select>
        <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
        <select name="status"><option value="Н">Н (Отсутствовал)</option><option value="Б">Б (Болел)</option><option value="У">У (Уважительная)</option></select>
        <button type="submit">Отметить</button>
    </form>
    <table>
        <tr><th>Студент</th><th>Предмет</th><th>Дата</th><th>Статус</th></tr>
        <?php foreach($logs as $l): ?>
        <tr><td><?php echo htmlspecialchars($l['full_name']); ?></td><td><?php echo htmlspecialchars($l['title']); ?></td><td><?php echo $l['date']; ?></td><td><b><?php echo $l['status']; ?></b></td></tr>
        <?php endforeach; ?>
    </table>
</div>
</body></html>