<?php
require 'auth.php';

// Очищаем сессию
$_SESSION = array();
session_destroy();

// Перекидываем на страницу входа
header('Location: login.php');
exit;
?>