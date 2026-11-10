<?php
require 'auth.php';
if (!isset($_SESSION['user'])) { header('Location: login.php'); exit; }
require_once 'classes/Database.php';

$user = $_SESSION['user'];
$db = new Database();

$totalStudents = $db->query("SELECT COUNT(*) FROM students")->fetchColumn();
$totalGrades = $db->query("SELECT COUNT(*) FROM grades")->fetchColumn();
$avgAll = $db->query("SELECT AVG(grade) FROM grades")->fetchColumn();
$avgAll = $avgAll ? round($avgAll, 2) : 0;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Электронный журнал</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f7f6; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #3498db; padding-bottom: 10px; margin-bottom: 20px; }
        h1 { margin: 0; color: #2c3e50; font-size: 24px; }
        .user-info { background: #ecf0f1; padding: 10px 15px; border-radius: 5px; font-weight: bold; }
        .dashboard { display: flex; gap: 20px; margin-bottom: 30px; }
        .stat-card { flex: 1; background: #2980b9; color: white; padding: 15px; border-radius: 8px; text-align: center; }
        .stat-card h3 { margin: 0 0 10px 0; font-size: 14px; font-weight: normal; opacity: 0.9; }
        .stat-card .number { font-size: 28px; font-weight: bold; }
        .menu { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .card { display: block; padding: 20px; background: #f8f9fa; border: 2px solid #dee2e6; border-radius: 8px; text-decoration: none; color: #333; transition: 0.2s; }
        .card:hover { background: #e9ecef; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .card h3 { margin-top: 0; margin-bottom: 5px; }
        .card p { margin: 0; font-size: 14px; color: #666; }
        .card-blue h3 { color: #2980b9; } .card-orange h3 { color: #e67e22; } .card-green h3 { color: #27ae60; }
        .card-purple h3 { color: #8e44ad; } .card-dark h3 { color: #2c3e50; } .card-red h3 { color: #c0392b; }
        .logout { display: inline-block; margin-top: 20px; color: #e74c3c; text-decoration: none; font-weight: bold; }
        .role-badge { font-size: 12px; background: #3498db; color: white; padding: 2px 8px; border-radius: 10px; margin-left: 10px; text-transform: uppercase; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Электронный журнал</h1>
        <div class="user-info"><?php echo htmlspecialchars($user['login']); ?><span class="role-badge"><?php echo htmlspecialchars($user['role']); ?></span></div>
    </div>

    <div class="dashboard">
        <div class="stat-card" style="background:#3498db;"><h3>Студентов в базе</h3><div class="number"><?php echo $totalStudents; ?></div></div>
        <div class="stat-card" style="background:#27ae60;"><h3>Всего оценок</h3><div class="number"><?php echo $totalGrades; ?></div></div>
        <div class="stat-card" style="background:#e67e22;"><h3>Средний балл по журналу</h3><div class="number"><?php echo $avgAll; ?></div></div>
    </div>

    <div class="menu">
        <!-- Доступно всем -->
        <a href="grades.php" class="card card-blue" style="border-color: #3498db;"><h3>Оценки</h3><p>Просмотр журнала оценок</p></a>
        <a href="reports.php" class="card card-blue" style="border-color: #3498db;"><h3>Отчёты</h3><p>Средние баллы и пропуски</p></a>
        
        <!-- Только для студентов -->
        <?php if ($user['role'] === 'student'): ?>
            <a href="profile.php" class="card card-blue" style="border-color: #3498db;"><h3>Мой профиль</h3><p>Личная карточка и статистика</p></a>
        <?php endif; ?>

        <!-- Для админов и учителей -->
        <?php if (in_array($user['role'], ['admin', 'teacher'])): ?>
            <a href="attendance.php" class="card card-orange" style="border-color: #e67e22;"><h3>Посещаемость</h3><p>Управление пропусками</p></a>
            <a href="add_grade.php" class="card card-green" style="border-color: #27ae60;"><h3>Добавить оценку</h3><p>Выставить новую оценку</p></a>
        <?php endif; ?>

        <!-- Только для админа -->
        <?php if ($user['role'] === 'admin'): ?>
            <a href="students.php" class="card card-purple" style="border-color: #8e44ad;"><h3>Студенты</h3><p>Список и управление студентами</p></a>
            <a href="subjects.php" class="card card-orange" style="border-color: #e67e22;"><h3>Предметы</h3><p>Управление списком дисциплин</p></a>
            <a href="users.php" class="card card-dark" style="border-color: #2c3e50;"><h3>Пользователи</h3><p>Добавление и удаление аккаунтов</p></a>
            <a href="logs.php" class="card card-dark" style="border-color: #2c3e50;"><h3>Журнал действий</h3><p>История событий в системе</p></a>
        <?php endif; ?>

        <!-- Экспорт (в самом конце) -->
        <?php if (in_array($user['role'], ['admin', 'teacher'])): ?>
            <a href="export_excel.php" class="card card-green" style="border-color: #1d6f42;"><h3>Экспорт в Excel</h3><p>Скачать ведомость (.xls)</p></a>
            <a href="export_pdf.php" class="card card-red" style="border-color: #c0392b;" target="_blank"><h3>Экспорт в PDF</h3><p>Печать ведомости</p></a>
            <?php if ($user['role'] === 'admin'): ?>
                <a href="export.php" class="card card-orange" style="border-color: #d35400;"><h3>Экспорт в CSV</h3><p>Скачать базу в CSV</p></a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <a href="logout.php" class="logout">Выйти из системы</a>
</div>
</body>
</html>