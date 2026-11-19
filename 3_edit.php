<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

$file = __DIR__ . '/xml_journal/students.xml';

$dom = new DOMDocument();
$dom->load($file);
$dom->formatOutput = true;

$students = $dom->getElementsByTagName('student');
$found = false;

foreach ($students as $student) {
    if ($student->getAttribute('id') == '99') {
        $name = $student->getElementsByTagName('name')->item(0);
        $name->nodeValue = 'ИВАНОВ ИЗМЕНЕН (ЧЕРЕЗ DOM)';
        $found = true;
        break;
    }
}

if ($found) {
    $dom->save($file);
    echo "<h1 style='color:green;'>УСПЕХ! Студент ID=99 ИЗМЕНЕН!</h1>";
} else {
    echo "<h1 style='color:red;'>Студент с ID=99 не найден!</h1>";
}