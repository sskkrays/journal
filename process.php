<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Результат регистрации</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f7f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .container { background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: center; max-width: 500px; width: 100%; }
        h1 { color: #2c3e50; }
        .error { color: #e74c3c; background: #fadbd8; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #e74c3c; }
        .success { color: #27ae60; background: #d5f5e3; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #27ae60; }
        .card { text-align: left; background: #ecf0f1; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .card p { margin: 10px 0; font-size: 16px; }
        .back-link { display: inline-block; margin-top: 10px; padding: 10px 20px; background: #34495e; color: white; text-decoration: none; border-radius: 5px; transition: background 0.3s; }
        .back-link:hover { background: #2c3e50; }
    </style>
</head>
<body>
<div class="container">
<?php
    // 1. Проверка, что запрос выполнен методом POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        // Если зашли напрямую по ссылке process.php
        echo "<div class='error'>";
        echo "<h1>Доступ запрещён!</h1>";
        echo "<p>Вы должны отправить данные через форму.</p>";
        echo "</div>";
        echo "<a href='form.html' class='back-link'>Перейти к форме</a>";
        echo "</div></body></html>";
        exit; // Завершаем работу скрипта
    }

    // 2. Получение данных из массива $_POST
    // Используем оператор ?? (если переменной нет, вернет пустую строку), чтобы избежать ошибок, если поле не отправлено
    $firstname = $_POST['firstname'] ?? '';
    $lastname  = $_POST['lastname'] ?? '';
    $age       = $_POST['age'] ?? '';
    $group     = $_POST['group'] ?? '';

    // 3. Очистка строковых данных через htmlspecialchars (защита от XSS)
    // Приводим возраст к целому числу (int)
    $firstnameClean = htmlspecialchars($firstname, ENT_QUOTES, 'UTF-8');
    $lastnameClean  = htmlspecialchars($lastname, ENT_QUOTES, 'UTF-8');
    $groupClean     = htmlspecialchars($group, ENT_QUOTES, 'UTF-8');
    $ageInt         = (int)$age;

    // 4. Валидация данных
    $errors = [];

    // Проверка на пустые поля (используем empty, но помним, что empty('0') это true, для возраста проверим отдельно)
    if (empty(trim($firstname))) {
        $errors[] = "Поле 'Имя' не может быть пустым.";
    }
    if (empty(trim($lastname))) {
        $errors[] = "Поле 'Фамилия' не может быть пустым.";
    }
    if (empty(trim($group))) {
        $errors[] = "Поле 'Группа' не может быть пустым.";
    }
    if ($age === '') {
        $errors[] = "Поле 'Возраст' не может быть пустым.";
    }

    // Проверка возраста (от 16 до 100)
    if ($age !== '' && ($ageInt < 16 || $ageInt > 100)) {
        $errors[] = "Возраст должен быть в диапазоне от 16 до 100 лет.";
    }

    // 5. Вывод результата
    if (count($errors) > 0) {
        // Если есть ошибки, выводим их списком
        echo "<h1 style='color: #e74c3c;'>Ошибка регистрации</h1>";
        echo "<div class='error'>";
        echo "<ul style='text-align: left; margin: 0; padding-left: 20px;'>";
        foreach ($errors as $error) {
            echo "<li>" . $error . "</li>";
        }
        echo "</ul>";
        echo "</div>";
    } else {
        // Если ошибок нет — выводим карточку студента
        echo "<h1 class='success'>Регистрация успешна!</h1>";
        echo "<div class='card'>";
        echo "<p><strong>Имя:</strong> " . $firstnameClean . "</p>";
        echo "<p><strong>Фамилия:</strong> " . $lastnameClean . "</p>";
        echo "<p><strong>Возраст:</strong> " . $ageInt . " лет</p>";
        echo "<p><strong>Группа:</strong> " . $groupClean . "</p>";
        echo "</div>";
    }

    // 6. Ссылка возврата
    echo "<a href='form.html' class='back-link'>Вернуться к форме</a>";
?>
</div>
</body>
</html>