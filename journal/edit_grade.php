<?php
require_once 'classes/Database.php';
require_once 'auth.php';
require_role(['admin', 'teacher']);

$db = new Database();
$id = (int)($_GET['id'] ?? 0);
$gradeData = $db->query("SELECT * FROM grades WHERE id = ?", [$id])->fetch();

if (!$gradeData) { die("Оценка не найдена"); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newGrade = (int)($_POST['grade'] ?? 0);
    if ($newGrade >= 2 && $newGrade <= 5) {
        $db->query("UPDATE grades SET grade = ?, date = ? WHERE id = ?", [$newGrade, $_POST['date'], $id]);
        header("Location: grades.php"); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ru"><head><meta charset="UTF-8"><title>Редактировать</title>
<style>body{font-family:Arial;background:#f4f7f6;padding:20px}.container{max-width:400px;margin:0 auto;background:#fff;padding:30px;border-radius:10px;box-shadow:0 4px 15px rgba(0,0,0,0.1)}input{width:100%;padding:10px;margin-bottom:15px;box-sizing:border-box}button{width:100%;padding:10px;background:#3498db;color:white;border:none;border-radius:5px;cursor:pointer}</style></head><body>
<div class="container">
    <h2>Редактировать оценку</h2>
    <form method="POST">
        <label>Оценка (2-5):</label>
        <input type="number" name="grade" min="2" max="5" value="<?php echo $gradeData['grade']; ?>" required>
        <label>Дата:</label>
        <input type="date" name="date" value="<?php echo $gradeData['date']; ?>" required>
        <button type="submit">Сохранить</button>
    </form>
    <br><a href="grades.php" style="display:block;text-align:center;color:#3498db;">Отмена</a>
</div>
</body></html>