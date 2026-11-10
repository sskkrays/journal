<?php
// Запускаем сессию, если она еще не запущена
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Функция для проверки ролей (по ТЗ)
function require_role(array $roles): void {
    if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], $roles, true)) {
        http_response_code(403);
        die("<h2 style='color:red; text-align:center; margin-top:50px;'>Доступ запрещён (403)</h2><p style='text-align:center;'><a href='index.php'>Вернуться на главную</a></p>");
    }
}
?>