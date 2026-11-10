<?php
// Путь к файлу базы данных
$dbFile = __DIR__ . '/journal.db';

try {
    $pdo = new PDO("sqlite:" . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // 1. Создаем таблицу студентов (если нет)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS students (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            full_name TEXT NOT NULL,
            group_name TEXT NOT NULL
        )
    ");

    // 2. Создаем таблицу предметов (если нет)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS subjects (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL
        )
    ");

    // 3. Создаем таблицу оценок (если нет)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS grades (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            student_id INTEGER NOT NULL,
            subject_id INTEGER NOT NULL,
            grade INTEGER NOT NULL CHECK(grade BETWEEN 2 AND 5),
            date TEXT NOT NULL,
            FOREIGN KEY (student_id) REFERENCES students(id),
            FOREIGN KEY (subject_id) REFERENCES subjects(id)
        )
    ");

    // 4. Создаем таблицу пользователей (по ТЗ)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            login TEXT UNIQUE NOT NULL,
            password_hash TEXT NOT NULL,
            role TEXT NOT NULL CHECK(role IN ('admin', 'teacher', 'student')),
            student_id INTEGER,
            FOREIGN KEY (student_id) REFERENCES students(id)
        )
    ");

    // 5. Создаем таблицу посещаемости (по ТЗ)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS attendance (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            student_id INTEGER NOT NULL,
            subject_id INTEGER NOT NULL,
            date TEXT NOT NULL,
            status TEXT NOT NULL CHECK(status IN ('Н', 'Б', 'У')),
            FOREIGN KEY (student_id) REFERENCES students(id),
            FOREIGN KEY (subject_id) REFERENCES subjects(id)
        )
    ");

    // ==========================================
    // ТЕСТОВЫЕ ДАННЫЕ (Заполняются только 1 раз)
    // ==========================================
    // Проверяем, есть ли студенты. Если нет — создаем базовых
    if ($pdo->query("SELECT COUNT(*) FROM students")->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO students (full_name, group_name) VALUES ('Ниёле', 'ИСП-34'), ('Камилла', 'ИСП-34'), ('Иван', 'ИСП-32')");
        $pdo->exec("INSERT INTO subjects (title) VALUES ('Математика'), ('PHP-программирование'), ('Базы данных')");
    }

    // Проверяем, есть ли пользователи. Если нет — создаем admin, teacher и student
    if ($pdo->query("SELECT COUNT(*) FROM users")->fetchColumn() == 0) {
        // Создаем пароли с помощью безопасного хеширования
        $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
        $teacherPass = password_hash('teacher123', PASSWORD_DEFAULT);
        $studentPass = password_hash('student123', PASSWORD_DEFAULT);

        // Вставляем пользователей
        $stmt = $pdo->prepare("INSERT INTO users (login, password_hash, role, student_id) VALUES (?, ?, ?, ?)");
        $stmt->execute(['admin', $adminPass, 'admin', null]);
        $stmt->execute(['teacher', $teacherPass, 'teacher', null]);
        $stmt->execute(['student', $studentPass, 'student', 1]); // Привязываем к Ниёле (ID=1)
    }
    // 6. Создаем таблицу логов действий
    $pdo->exec("
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
?>