<?php
// Создаём пустой XML
$xml = new SimpleXMLElement('<students></students>');

// Добавляем первого студента
$s1 = $xml->addChild('student');
$s1->addAttribute('id', '1');
$s1->addChild('name', 'Иванов Иван');
$s1->addChild('group', 'ИС-41');

// Добавляем второго
$s2 = $xml->addChild('student');
$s2->addAttribute('id', '2');
$s2->addChild('name', 'Петрова Анна');
$s2->addChild('group', 'ИС-42');

// Сохраняем в файл new_students.xml
$xml->asXML('new_students.xml');

echo "Файл new_students.xml создан! Открой его в VS Code.";