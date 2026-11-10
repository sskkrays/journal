<?php
require_once 'classes/Database.php';
require_once 'auth.php';
if (!isset($_SESSION['user'])) { header('Location: login.php'); exit; }
$user = $_SESSION['user'];
$db = new Database();

if (isset($_GET['delete']) && in_array($user['role'], ['admin', 'teacher'])) {
    $db->query("DELETE FROM grades WHERE id = ?", [(int)$_GET['delete']]);
    header("Location: grades.php"); exit;
}

if ($user['role'] === 'student') {
    $stmt = $db->query("SELECT g.id, s.full_name, sub.title AS subject, g.grade, g.date FROM grades g JOIN students s ON s.id = g.student_id JOIN subjects sub ON sub.id = g.subject_id WHERE g.student_id = ? ORDER BY g.date DESC", [$user['student_id']]);
} else {
    $stmt = $db->query("SELECT g.id, s.full_name, sub.title AS subject, g.grade, g.date FROM grades g JOIN students s ON s.id = g.student_id JOIN subjects sub ON sub.id = g.subject_id ORDER BY g.date DESC");
}
$grades = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru"><head><meta charset="UTF-8"><title>Журнал оценок</title>
<style>body{font-family:Arial;background:#f4f7f6;padding:20px}.container{max-width:900px;margin:0 auto;background:#fff;padding:30px;border-radius:10px;box-shadow:0 4px 15px rgba(0,0,0,0.1)}table{width:100%;border-collapse:collapse;margin-top:20px}th,td{padding:12px;text-align:left;border-bottom:1px solid #ddd}th{background:#3498db;color:white}.btn-del{color:red;text-decoration:none}.btn-edit{color:blue;text-decoration:none;margin-right:10px}.back{display:inline-block;margin-bottom:20px;color:#3498db;text-decoration:none}</style></head><body>
<div class="container">
    <a href="index.php" class="back">← На главную</a>
    <h2>Журнал оценок</h2>
    <table>
        <tr><th>Студент</th><th>Предмет</th><th>Оценка</th><th>Дата</th><?php if(in_array($user['role'], ['admin','teacher'])) echo "<th>Действия</th>"; ?></tr>
        <?php foreach($grades as $row): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
            <td><?php echo htmlspecialchars($row['subject']); ?></td>
            <td><b><?php echo $row['grade']; ?></b></td>
            <td><?php echo $row['date']; ?></td>
            <?php if(in_array($user['role'], ['admin','teacher'])): ?>
            <td>
                <a href="edit_grade.php?id=<?php echo $row['id']; ?>" class="btn-edit">✏️</a>
                <a href="grades.php?delete=<?php echo $row['id']; ?>" class="btn-del" onclick="return confirm('Удалить?')">❌</a>
            </td>
            <?php endif; ?>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body></html>