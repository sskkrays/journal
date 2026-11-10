<?php
require_once 'classes/Database.php';
require_once 'auth.php';
require_role(['admin', 'teacher']);

$db = new Database();
$grades = $db->query("SELECT s.full_name, s.group_name, sub.title, g.grade, g.date FROM grades g JOIN students s ON s.id = g.student_id JOIN subjects sub ON sub.id = g.subject_id ORDER BY s.full_name")->fetchAll();

// Отдаем заголовки для скачивания файла Excel
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=journal_report.xls");

// ВАЖНО: Выводим BOM (Byte Order Mark) для правильного отображения кириллицы в Excel
echo "\xEF\xBB\xBF"; 
?>
<table border="1">
    <tr>
        <th>Студент</th><th>Группа</th><th>Предмет</th><th>Оценка</th><th>Дата</th>
    </tr>
    <?php foreach($grades as $row): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
        <td><?php echo htmlspecialchars($row['group_name']); ?></td>
        <td><?php echo htmlspecialchars($row['title']); ?></td>
        <td><?php echo $row['grade']; ?></td>
        <td><?php echo $row['date']; ?></td>
    </tr>
    <?php endforeach; ?>
</table>