<?php
require_once 'classes/Database.php';
require_once 'auth.php';
require_role(['admin', 'teacher']);

$db = new Database();
$grades = $db->query("SELECT s.full_name, s.group_name, sub.title, g.grade, g.date FROM grades g JOIN students s ON s.id = g.student_id JOIN subjects sub ON sub.id = g.subject_id ORDER BY s.full_name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru"><head><meta charset="UTF-8"><title>Отчет для печати</title>
<style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #000; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .no-print { margin-bottom: 20px; }
    @media print { .no-print { display: none; } }
</style></head><body>
<div class="no-print">
    <button onclick="window.print()" style="padding:10px 20px;font-size:16px;cursor:pointer;">🖨️ Сохранить в PDF</button>
    <a href="index.php" style="margin-left:20px;">← Назад</a>
</div>
<h2>Ведомость оценок</h2>
<table>
    <thead><tr><th>Студент</th><th>Группа</th><th>Предмет</th><th>Оценка</th><th>Дата</th></tr></thead>
    <tbody>
        <?php foreach($grades as $row): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
            <td><?php echo htmlspecialchars($row['group_name']); ?></td>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo $row['grade']; ?></td>
            <td><?php echo $row['date']; ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</body></html>