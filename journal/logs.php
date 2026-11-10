<?php
require_once 'classes/Database.php';
require_once 'auth.php';
require_role(['admin']); // Только админ может видеть логи

$db = new Database();
$logs = $db->query("SELECT * FROM logs ORDER BY created_at DESC LIMIT 100")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Журнал действий</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f7f6; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h2 { color: #2c3e50; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background-color: #2c3e50; color: white; }
        .back { display: inline-block; margin-bottom: 20px; color: #3498db; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
<div class="container">
    <a href="index.php" class="back">← На главную</a>
    <h2>Журнал действий (Последние 100)</h2>
    <?php if (empty($logs)): ?>
        <p>Логов пока нет.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr><th>Дата и время</th><th>Пользователь</th><th>Действие</th></tr>
            </thead>
            <tbody>
                <?php foreach($logs as $log): ?>
                <tr>
                    <td><?php echo $log['created_at']; ?></td>
                    <td><strong><?php echo htmlspecialchars($log['user_login']); ?></strong></td>
                    <td><?php echo htmlspecialchars($log['action']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>