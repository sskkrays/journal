<?php
require_once 'classes/Database.php';
require_once 'auth.php';
require_role(['admin']);

$db = new Database();
$grades = $db->query("SELECT s.full_name, sub.title, g.grade, g.date FROM grades g JOIN students s ON s.id = g.student_id JOIN subjects sub ON sub.id = g.subject_id ORDER BY s.full_name")->fetchAll();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="journal.csv"');
$out = fopen('php://output', 'w');
fputcsv($out, ['Студент', 'Предмет', 'Оценка', 'Дата']);

foreach ($grades as $row) {
    fputcsv($out, [$row['full_name'], $row['title'], $row['grade'], $row['date']]);
}
fclose($out);
exit;
?>