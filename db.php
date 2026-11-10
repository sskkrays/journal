<?php
// Настройки базы данных (SQLite)
$dbFile = __DIR__ . '/students.db'; // Файл базы данных будет лежать рядом со скриптом

$errorMsg = '';
$students = [];

try {
    // 1. Подключение к базе данных через PDO
    // Если файла students.db нет, SQLite автоматически создаст его!
    $pdo = new PDO("sqlite:" . $dbFile);
    
    // 2. Устанавливаем режим обработки ошибок
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 3. Создаем таблицу students, если её еще нет
    $sqlCreateTable = "
        CREATE TABLE IF NOT EXISTS students (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            full_name TEXT NOT NULL,
            group_name TEXT NOT NULL
        )
    ";
    $pdo->exec($sqlCreateTable);

    // 4. Проверяем, есть ли данные в таблице. Если пусто — добавляем записи.
    $count = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
    
    if ($count == 0) {
        // Используем подготовленные выражения для безопасности
        $stmtInsert = $pdo->prepare("INSERT INTO students (full_name, group_name) VALUES (?, ?)");
        
        // Добавляем 4 записи
        $stmtInsert->execute(["Ниёле", "ИСП-34"]);
        $stmtInsert->execute(["Камилла", "ИСП-34"]);
        $stmtInsert->execute(["Алексей", "ИСП-32"]);
        $stmtInsert->execute(["Мария", "ИСП-33"]);
    }

    // 5. Выполняем запрос SELECT
    $stmt = $pdo->query("SELECT id, full_name, group_name FROM students");
    
    // 6. Получаем все строки в виде ассоциативного массива
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Обработка исключений (если что-то пошло не так)
    $errorMsg = "Ошибка базы данных: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Список студентов (PDO + SQLite)</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f7f6; padding: 20px; color: #333; }
        .container { max-width: 800px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #3498db; color: white; font-weight: bold; }
        tr:hover { background-color: #f1f1f1; }
        .error { color: #e74c3c; background: #fadbd8; padding: 15px; border-radius: 5px; border: 1px solid #e74c3c; }
        .success-note { text-align: center; color: #27ae60; font-size: 14px; margin-bottom: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h1>База данных студентов</h1>

    <?php if ($errorMsg): ?>
        <div class="error"><?php echo htmlspecialchars($errorMsg); ?></div>
    <?php else: ?>
        <p class="success-note">Успешное подключение к базе данных SQLite.</p>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ФИО</th>
                    <th>Группа</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($students) > 0): ?>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <!-- 7. Экранирование вывода через htmlspecialchars() -->
                            <td><?php echo htmlspecialchars($student['id']); ?></td>
                            <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($student['group_name']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" style="text-align:center;">В базе данных пока нет студентов.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>