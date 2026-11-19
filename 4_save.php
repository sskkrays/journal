<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

$file = __DIR__ . '/xml_journal/students.xml';
$backup = __DIR__ . '/xml_journal/students_backup.xml';

$xml = simplexml_load_file($file);
if ($xml === false) {
    die("<h1 style='color:red;'>Ошибка: не удалось прочитать файл.</h1>");
}

if ($xml->asXML($backup)) {
    echo "<h1 style='color:green;'>УСПЕХ! Копия сохранена!</h1>";
    echo "<p>Файл students_backup.xml создан в папке xml_journal.</p>";
} else {
    echo "<h1 style='color:red;'>Ошибка сохранения.</h1>";
}