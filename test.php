<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "<h1>Я работаю!</h1>";
echo "<p>Текущая папка: " . __DIR__ . "</p>";

// Проверяем, где лежит файл students.xml
$paths = [
    __DIR__ . '/students.xml',
    __DIR__ . '/xml_journal/students.xml',
    __DIR__ . '/journal/xml_journal/students.xml'
];

$found = false;
foreach ($paths as $path) {
    if (file_exists($path)) {
        echo "<p style='color:green'>Нашел файл: $path</p>";
        $xml = simplexml_load_file($path);
        
        // Добавляем 99-го
        $s = $xml->addChild('student');
        $s->addAttribute('id', '99');
        $s->addAttribute('status', 'active');
        $s->addChild('name', 'СРАБОТАЛО! Я ДОБАВЛЕН ЧЕРЕЗ PHP');
        $s->addChild('group', 'ИС-99');
        
        // Сохраняем
        if ($xml->asXML($path)) {
            echo "<h2 style='color:green'>УСПЕХ! Файл сохранен!</h2>";
        } else {
            echo "<h2 style='color:red'>ОШИБКА: Не могу сохранить файл (нет прав)!</h2>";
        }
        $found = true;
        break;
    }
}

if (!$found) {
    echo "<h2 style='color:red'>ФАЙЛ students.xml НЕ НАЙДЕН НИ ПО ОДНОМУ ПУТИ!</h2>";
}