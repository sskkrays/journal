<?php
require_once __DIR__ . '/Database.php';

class Attendance
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Добавление записи о посещаемости
     */
    public function addRecord(int $studentId, int $subjectId, string $date, string $status): bool
    {
        $sql = "INSERT INTO attendance (student_id, subject_id, date, status) VALUES (?, ?, ?, ?)";
        
        try {
            $this->db->query($sql, [$studentId, $subjectId, $date, $status]);
            return true;
        } catch (PDOException $e) {
            // Обработка ошибки (например, неверный статус или дата)
            return false;
        }
    }
}