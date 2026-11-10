<?php
require_once 'classes/Database.php';
require_once 'auth.php';
if (!isset($_SESSION['user'])) { header('Location: login.php'); exit; }

$user = $_SESSION['user'];
$db = new Database();

// Получаем информацию о студенте
$studentInfo = $db->query("SELECT * FROM students WHERE id = ?", [$user['student_id']])->fetch();

if (!$studentInfo) { die("Информация о студенте не найдена."); }

// Берем первую букву имени безопасно для кириллицы
// Если mb_substr не работает (нет расширения), используем запасной вариант
if (function_exists('mb_substr')) {
    $firstLetter = mb_substr($studentInfo['full_name'], 0, 1, 'UTF-8');
} else {
    // Запасной вариант для Windows без mbstring
    $firstLetter = substr($studentInfo['full_name'], 0, 2);
    $firstLetter = iconv('UTF-8', 'UTF-8//IGNORE', $firstLetter);
}

// Собираем статистику
$avg = $db->query("SELECT AVG(grade) FROM grades WHERE student_id = ?", [$user['student_id']])->fetchColumn();
$avg = $avg ? round($avg, 2) : 0;
$totalGrades = $db->query("SELECT COUNT(*) FROM grades WHERE student_id = ?", [$user['student_id']])->fetchColumn();
$misses = $db->query("SELECT COUNT(*) FROM attendance WHERE student_id = ? AND status = 'Н'", [$user['student_id']])->fetchColumn();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Мой профиль</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f7f6; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .header { border-bottom: 2px solid #3498db; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #2c3e50; font-size: 24px; }
        .student-card { display: flex; align-items: center; gap: 20px; margin-bottom: 30px; }
        .avatar { width: 80px; height: 80px; background: #3498db; color: white; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 32px; font-weight: bold; }
        .student-info h2 { margin: 0 0 5px 0; color: #2c3e50; }
        .student-info p { margin: 0; color: #7f8c8d; font-size: 16px; }
        .stats-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; }
        .stat-box { background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center; border: 1px solid #eee; }
        .stat-box h4 { margin: 0 0 10px 0; color: #7f8c8d; font-size: 12px; text-transform: uppercase; }
        .stat-box .value { font-size: 24px; font-weight: bold; color: #2980b9; }
        .back { display: inline-block; margin-top: 20px; color: #3498db; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
<div class="container">
    <a href="index.php" class="back">← На главную</a>
    <div class="header">
        <h1>Личный кабинет</h1>
    </div>

    <div class="student-card">
        <!-- ТУТ ВЫВОДИТСЯ ПЕРВАЯ БУКВА -->
        <div class="avatar"><?php echo $firstLetter; ?></div>
        <div class="student-info">
            <h2><?php echo htmlspecialchars($studentInfo['full_name']); ?></h2>
            <p>Группа: <strong><?php echo htmlspecialchars($studentInfo['group_name']); ?></strong></p>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-box">
            <h4>Средний балл</h4>
            <div class="value" style="color: <?php echo ($avg >= 4.5) ? '#27ae60' : (($avg < 3) ? '#e74c3c' : '#e67e22'); ?>;">
                <?php echo $avg; ?>
            </div>
        </div>
        <div class="stat-box">
            <h4>Оценок</h4>
            <div class="value"><?php echo $totalGrades; ?></div>
        </div>
        <div class="stat-box">
            <h4>Пропусков (Н)</h4>
            <div class="value" style="color: <?php echo ($misses > 0) ? '#e74c3c' : '#27ae60'; ?>;">
                <?php echo $misses; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>