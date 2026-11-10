<?php
require_once 'classes/Database.php';
require_once 'auth.php';
if (!isset($_SESSION['user'])) { header('Location: login.php'); exit; }

$db = new Database();
$avgGrades = $db->query("SELECT s.full_name, AVG(g.grade) as avg_grade FROM grades g JOIN students s ON s.id = g.student_id GROUP BY s.id ORDER BY avg_grade DESC")->fetchAll();
$misses = $db->query("SELECT s.full_name, COUNT(a.id) as miss_count FROM attendance a JOIN students s ON s.id = a.student_id WHERE a.status = 'Н' GROUP BY s.id ORDER BY miss_count DESC")->fetchAll();
$subjectAvgs = $db->query("SELECT sub.title AS subject, AVG(g.grade) AS avg_grade FROM grades g JOIN subjects sub ON sub.id = g.subject_id GROUP BY sub.id")->fetchAll();

$chartLabels = [];
$chartData = [];
foreach ($subjectAvgs as $row) {
    $chartLabels[] = $row['subject'];
    $chartData[] = round($row['avg_grade'], 2);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Отчеты</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f7f6; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h2 { color: #2c3e50; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background-color: #2c3e50; color: white; }
        .back { display: inline-block; margin-bottom: 20px; color: #3498db; text-decoration: none; font-weight: bold; }
        .chart-container { position: relative; height: 300px; width: 100%; margin: 20px 0; }
    </style>
</head>
<body>
<div class="container">
    <a href="index.php" class="back">← На главную</a>
    <h2>Средний балл по предметам (График)</h2>
    <div class="chart-container"><canvas id="gradesChart"></canvas></div>
</div>
<div class="container">
    <h2>Средний балл студентов</h2>
    <table><tr><th>Студент</th><th>Средний балл</th></tr><?php foreach($avgGrades as $r): ?><tr><td><?php echo htmlspecialchars($r['full_name']); ?></td><td><?php echo round($r['avg_grade'], 2); ?></td></tr><?php endforeach; ?></table>
</div>
<div class="container">
    <h2>Количество пропусков (Н)</h2>
    <table><tr><th>Студент</th><th>Пропусков</th></tr><?php foreach($misses as $r): ?><tr><td><?php echo htmlspecialchars($r['full_name']); ?></td><td><?php echo $r['miss_count']; ?></td></tr><?php endforeach; ?></table>
</div>
<script>
    const ctx = document.getElementById('gradesChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($chartLabels); ?>,
            datasets: [{ label: 'Средний балл', data: <?php echo json_encode($chartData); ?>, backgroundColor: ['rgba(52, 152, 219, 0.7)','rgba(46, 204, 113, 0.7)','rgba(241, 196, 15, 0.7)','rgba(231, 76, 60, 0.7)','rgba(155, 89, 182, 0.7)'], borderWidth: 1 }]
        },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, max: 5, ticks: { stepSize: 1 } } } }
    });
</script>
</body>
</html>