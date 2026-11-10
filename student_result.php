<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Результат студента</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f7f6; padding: 20px; color: #333; }
        .container { max-width: 700px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; }
        h2 { color: #2980b9; margin-top: 30px; }
        ul { list-style-type: none; padding: 0; }
        li { padding: 8px 0; border-bottom: 1px dashed #ccc; }
        li:last-child { border-bottom: none; }
        .grades span { display: inline-block; background: #ecf0f1; padding: 5px 12px; margin-right: 10px; border-radius: 5px; font-weight: bold; }
        .result-box { margin-top: 20px; padding: 15px; border-radius: 8px; font-size: 18px; font-weight: bold; text-align: center; }
        .excellent { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .good { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .satisfactory { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .bad { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
<div class="container">
<?php
    // ===== 1. Объявление переменных =====
    $fullName     = "Ниёле и Камилла";
    $age          = 18;
    $group        = "ИСП-34";
    $speciality   = "Информационные системы и программирование";
    
    // Оценки студента
    $grade1 = 5;
    $grade2 = 4;
    $grade3 = 5;
    // Можно изменить оценки на 3, 3, 4 и посмотреть, как изменится статус

    // ===== 2. Вычисления =====
    $countGrades = 3;
    $sumGrades   = $grade1 + $grade2 + $grade3;
    $average     = $sumGrades / $countGrades;

    // Округляем средний балл до 2 знаков после запятой для красоты
    $averageFormatted = round($average, 2);

    // ===== 3. Логика (Определение статуса) =====
    $statusText = "";
    $statusClass = "";

    if ($average >= 4.5) {
        $statusText = "Отличный результат";
        $statusClass = "excellent";
    } elseif ($average >= 3.5) {
        $statusText = "Хороший результат";
        $statusClass = "good";
    } elseif ($average >= 3.0) {
        $statusText = "Удовлетворительно";
        $statusClass = "satisfactory";
    } else {
        $statusText = "Нужно улучшить";
        $statusClass = "bad";
    }

    // Дополнительная проверка с логическим оператором И (&&)
    // Если средний балл высокий И нет двоек, добавляем пометку
    $extraNote = "";
    if ($average >= 4.5 && $grade1 > 2 && $grade2 > 2 && $grade3 > 2) {
        $extraNote = " (Студент может претендовать на стипендию!)";
    }

    // ===== 4. Вывод HTML через echo =====
    echo "<h1>Отчёт об успеваемости</h1>";
    
    echo "<h2>Личные данные</h2>";
    echo "<ul>";
    echo "<li><strong>ФИО:</strong> " . $fullName . "</li>";
    echo "<li><strong>Возраст:</strong> " . $age . " лет</li>";
    echo "<li><strong>Группа:</strong> " . $group . "</li>";
    echo "<li><strong>Специальность:</strong> " . $speciality . "</li>";
    echo "</ul>";

    echo "<h2>Успеваемость</h2>";
    echo "<div class='grades'>";
    echo "<span>Оценка 1: " . $grade1 . "</span>";
    echo "<span>Оценка 2: " . $grade2 . "</span>";
    echo "<span>Оценка 3: " . $grade3 . "</span>";
    echo "</div>";
    
    echo "<p style='margin-top: 15px;'><strong>Средний балл:</strong> " . $averageFormatted . "</p>";

    echo "<h2>Итоговый результат</h2>";
    echo "<div class='result-box " . $statusClass . "'>";
    echo $statusText . $extraNote;
    echo "</div>";
?>
</div>
</body>
</html>