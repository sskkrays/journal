<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Путь к твоему файлу (он лежит в папке xml_journal)
$file = __DIR__ . '/xml_journal/students.xml';

$xml = simplexml_load_file($file);
if ($xml === false) {
    die("Ошибка: не найден файл students.xml по пути: $file");
}

// Добавляем студента
$s = $xml->addChild('student');
$s->addAttribute('id', '99');
$s->addAttribute('status', 'active');
$s->addChild('name', 'СРАБОТАЛО! Я ДОБАВЛЕН ЧЕРЕЗ PHP');
$s->addChild('group', 'ИС-99');

if ($xml->asXML($file)) {
    echo "<h1 style='color:green;'>УСПЕХ! Файл сохранен!</h1>";
} else {
    echo "<h1 style='color:red;'>ОШИБКА: Не могу сохранить файл!</h1>";
}