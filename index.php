<?php
// Имя файла для хранения журнала
$filename = 'visits.txt';

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем имя и сразу убираем лишние пробелы по краям
    $studentName = trim($_POST['student_name'] ?? '');

    // Проверяем, что имя не пустое
    if (!empty($studentName)) {
        // 1. Проверяем, существует ли файл (по ТЗ)
        if (!file_exists($filename)) {
            // Если файла нет, создаем его пустым (опционально, file_put_contents с FILE_APPEND создаст его сам)
            file_put_contents($filename, '');
        }

        // 2. Добавляем имя в конец файла с новой строки
        // Флаг FILE_APPEND означает "дописать в конец", а не перезаписать
        file_put_contents($filename, $studentName . "\n", FILE_APPEND);

        // Перенаправляем на эту же страницу (PRG - Post/Redirect/Get),
        // чтобы при обновлении страницы (F5) форма не отправлялась повторно
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $error = "Пожалуйста, введите имя.";
    }
}

// Чтение данных для вывода
$visits = [];
$totalVisits = 0;

// 3. Проверяем существование файла перед чтением
if (file_exists($filename)) {
    // Читаем весь файл
    $content = file_get_contents($filename);
    
    // Если файл не пустой, разбиваем его на массив строк
    if (trim($content) !== '') {
        // FILE_IGNORE_NEW_LINES удаляет символы переноса строки из массива
        // FILE_SKIP_EMPTY_LINES пропускает пустые строки
        $visits = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $totalVisits = count($visits);
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Журнал посещений</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f7f6; padding: 20px; color: #333; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; text-align: center; }
        h2 { color: #2980b9; margin-top: 30px; }
        .form-group { display: flex; gap: 10px; margin-bottom: 20px; }
        input[type="text"] { flex-grow: 1; padding: 10px; border: 1px solid #bdc3c7; border-radius: 5px; font-size: 16px; }
        input[type="text"]:focus { border-color: #3498db; outline: none; }
        button { padding: 10px 20px; background: #3498db; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; transition: background 0.3s; font-weight: bold; }
        button:hover { background: #2980b9; }
        .error { color: #e74c3c; background: #fadbd8; padding: 10px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #e74c3c; }
        .list-container { background: #ecf0f1; padding: 20px; border-radius: 8px; }
        ol { padding-left: 20px; margin: 0; }
        li { padding: 8px 0; border-bottom: 1px dashed #bdc3c7; font-size: 16px; }
        li:last-child { border-bottom: none; }
        .total { margin-top: 15px; font-weight: bold; font-size: 18px; color: #27ae60; text-align: right; }
        .empty-message { color: #7f8c8d; font-style: italic; text-align: center; }
        .refresh-link { display: block; text-align: center; margin-top: 20px; color: #3498db; text-decoration: none; font-weight: bold; }
        .refresh-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="container">
    <h1>Журнал посещений</h1>

    <!-- Вывод ошибки, если поле было пустым -->
    <?php if (isset($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Форма добавления -->
    <form action="index.php" method="POST">
        <div class="form-group">
            <input type="text" name="student_name" placeholder="Введите имя студента (например, Ниёле)" autocomplete="off">
            <button type="submit">Отправить</button>
        </div>
    </form>

    <!-- Вывод журнала -->
    <h2>Список посещений:</h2>
    <div class="list-container">
        <?php if ($totalVisits > 0): ?>
            <ol>
                <?php 
                // Выводим каждое имя. Обязательно используем htmlspecialchars для защиты от XSS!
                foreach ($visits as $name) {
                    echo "<li>" . htmlspecialchars($name) . "</li>";
                }
                ?>
            </ol>
            <div class="total">Всего посещений: <?php echo $totalVisits; ?></div>
        <?php else: ?>
            <!-- Обработка случая, когда файл пуст или его еще нет -->
            <p class="empty-message">Журнал пока пуст. Станьте первым!</p>
        <?php endif; ?>
    </div>

    <!-- Дополнительно: ссылка на перезагрузку -->
    <a href="index.php" class="refresh-link">🔄 Обновить список</a>
</div>
</body>
</html>