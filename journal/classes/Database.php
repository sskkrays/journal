<?php

class Database
{
    private PDO $pdo;

    public function __construct()
    {
        $dbFile = __DIR__ . '/../journal.db';
        
        try {
            $dsn = "sqlite:" . $dbFile;
            $this->pdo = new PDO($dsn);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // Автоматически создаем таблицу логов, если её еще нет
            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS logs (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_login TEXT NOT NULL,
                    action TEXT NOT NULL,
                    created_at TEXT NOT NULL
                )
            ");

        } catch (PDOException $e) {
            die("Ошибка подключения к БД: " . $e->getMessage());
        }
    }

    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}