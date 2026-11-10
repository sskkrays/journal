<?php
require_once __DIR__ . '/Database.php';

class User
{
    public int $id;
    public string $login;
    public string $role;
    public ?int $studentId; // Может быть null (для админов и учителей)

    /**
     * Статический метод: вызывается без создания экземпляра (User::findByLogin)
     * Ищет пользователя в БД по логину и возвращает готовый объект User
     */
    public static function findByLogin(string $login, Database $db): ?User
    {
        $sql = "SELECT * FROM users WHERE login = ?";
        
        // Используем защищенный запрос через наш класс Database
        $stmt = $db->query($sql, [$login]);
        $data = $stmt->fetch();

        if (!$data) {
            return null; // Пользователь не найден
        }

        // Создаем новый объект User и заполняем его свойства
        $user = new self();
        $user->id = (int)$data['id'];
        $user->login = $data['login'];
        $user->role = $data['role'];
        $user->studentId = $data['student_id'] ? (int)$data['student_id'] : null;

        return $user;
    }
}