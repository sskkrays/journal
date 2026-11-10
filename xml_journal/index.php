<?php
// 1. Безопасная функция вывода с "косметической" заменой тегов для красоты
function e($value) {
    $str = (string)$value;
    // Заменяем опасные теги на безопасные аналоги для визуала
    $str = str_ireplace('<script>', '[script]', $str);
    $str = str_ireplace('</script>', '[/script]', $str);
    $str = str_ireplace('<', '&lt;', $str);
    $str = str_ireplace('>', '&gt;', $str);
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// 2. Загрузка и валидация XML
$xmlFile = __DIR__ . '/students.xml';
$errorMessages = [];
$students = [];

if (file_exists($xmlFile)) {
    libxml_use_internal_errors(true);
    $xml = simplexml_load_file($xmlFile);
    
    if ($xml === false) {
        foreach (libxml_get_errors() as $error) {
            $errorMessages[] = "Строка {$error->line}: " . trim($error->message);
        }
        libxml_clear_errors();
    } else {
        foreach ($xml->student as $s) {
            $grades = [];
            $sum = 0;
            if (isset($s->grades->grade)) {
                foreach ($s->grades->grade as $g) {
                    $grades[] = (int)$g['value'];
                    $sum += (int)$g['value'];
                }
            }
            $avg = count($grades) > 0 ? round($sum / count($grades), 2) : null;

            $students[] = [
                'id' => (int)$s['id'],
                'status' => (string)$s['status'],
                'name' => (string)$s->name,
                'group' => (string)$s->group,
                'specialty_code' => (string)$s->specialty['code'],
                'specialty_name' => (string)$s->specialty,
                'email' => isset($s->email) ? (string)$s->email : null,
                'avg' => $avg
            ];
        }
    }
}

// 3. Фильтрация
$f_group = trim($_GET['group'] ?? '');
$f_search = trim($_GET['search'] ?? '');
$f_min_avg = trim($_GET['min_avg'] ?? '');
$f_status = $_GET['status'] ?? 'active';
if (!in_array($f_status, ['active', 'expelled', 'all'])) { $f_status = 'active'; }

$filtered = array_filter($students, function($s) use ($f_group, $f_search, $f_min_avg, $f_status) {
    if ($f_status !== 'all' && $s['status'] !== $f_status) { return false; }
    if ($f_group !== '' && strtolower($s['group']) !== strtolower($f_group)) { return false; }
    if ($f_search !== '' && stripos($s['name'], $f_search) === false) { return false; }
    if ($f_min_avg !== '' && is_numeric($f_min_avg)) {
        if ($s['avg'] === null || $s['avg'] < (float)$f_min_avg) { return false; }
    }
    return true;
});

// 4. Сортировка
$f_sort = $_GET['sort'] ?? 'name';
$f_dir = $_GET['dir'] ?? 'asc';
if (!in_array($f_sort, ['name', 'group', 'avg'])) { $f_sort = 'name'; }
if (!in_array($f_dir, ['asc', 'desc'])) { $f_dir = 'asc'; }

usort($filtered, function($a, $b) use ($f_sort, $f_dir) {
    if ($f_sort === 'avg') {
        $valA = $a['avg'] ?? -1;
        $valB = $b['avg'] ?? -1;
    } else {
        $valA = strtolower($a[$f_sort]);
        $valB = strtolower($b[$f_sort]);
    }
    $result = $valA <=> $valB;
    return $f_dir === 'asc' ? $result : -$result;
});

// 5. Пагинация
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 5;
$totalFiltered = count($filtered);
$totalPages = ceil($totalFiltered / $perPage);
if ($page > $totalPages && $totalPages > 0) { $page = 1; }

$offset = ($page - 1) * $perPage;
$itemsForPage = array_slice($filtered, $offset, $perPage);

// Считаем общий средний балл
$totalSum = 0;
$countWithGrades = 0;
foreach ($filtered as $s) {
    if ($s['avg'] !== null) {
        $totalSum += $s['avg'];
        $countWithGrades++;
    }
}
$globalAvg = $countWithGrades > 0 ? round($totalSum / $countWithGrades, 2) : '—';

function buildQuery($newPage) {
    $params = $_GET;
    $params['page'] = $newPage;
    return '?' . http_build_query($params);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Система ведомости (XML)</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f7f6; padding: 20px; color: #333; }
        .container { max-width: 1000px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; text-align: center; }
        .error-box { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb; }
        .error-box ul { margin: 0; padding-left: 20px; }
        fieldset { border: 1px solid #ccc; border-radius: 8px; padding: 15px; margin-bottom: 20px; display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end; }
        legend { font-weight: bold; color: #2980b9; padding: 0 5px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { font-size: 12px; margin-bottom: 5px; color: #666; }
        .form-group input, .form-group select { padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        .btn { padding: 9px 15px; background: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .btn:hover { background: #2980b9; }
        .btn-reset { background: #e74c3c; text-decoration: none; text-align: center; }
        .btn-reset:hover { background: #c0392b; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 14px; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #2c3e50; color: white; }
        th a { color: white; text-decoration: none; display: block; }
        th a:hover { text-decoration: underline; }
        tr.expelled { background-color: #fadbd8; color: #7f8c8d; }
        tr:hover { background-color: #f1f1f1; }
        .pagination { margin-top: 20px; text-align: center; }
        .pagination a, .pagination span { display: inline-block; padding: 8px 12px; margin: 0 2px; border: 1px solid #ddd; text-decoration: none; color: #333; border-radius: 4px; }
        .pagination a:hover { background: #eee; }
        .pagination .active { background: #3498db; color: white; border-color: #3498db; }
        .footer-info { margin-top: 20px; font-weight: bold; color: #2c3e50; text-align: right; font-size: 15px; }
    </style>
</head>
<body>
<div class="container">
    <h1>Ведомость студентов (XML)</h1>

    <?php if (!empty($errorMessages)): ?>
        <div class="error-box">
            <strong>Ошибка загрузки XML:</strong>
            <ul>
                <?php foreach ($errorMessages as $msg): ?><li><?php echo e($msg); ?></li><?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="GET">
        <fieldset>
            <legend>Фильтры и сортировка</legend>
            <div class="form-group"><label>Группа</label><input type="text" name="group" value="<?php echo e($f_group); ?>" placeholder="ИС-41"></div>
            <div class="form-group"><label>Поиск по ФИО</label><input type="text" name="search" value="<?php echo e($f_search); ?>" placeholder="Иванов"></div>
            <div class="form-group"><label>Мин. балл</label><input type="number" step="0.1" name="min_avg" value="<?php echo e($f_min_avg); ?>" placeholder="4.5"></div>
            <div class="form-group">
                <label>Статус</label>
                <select name="status">
                    <option value="active" <?php echo $f_status === 'active' ? 'selected' : ''; ?>>Активные</option>
                    <option value="expelled" <?php echo $f_status === 'expelled' ? 'selected' : ''; ?>>Отчисленные</option>
                    <option value="all" <?php echo $f_status === 'all' ? 'selected' : ''; ?>>Все</option>
                </select>
            </div>
            <div class="form-group">
                <label>Сортировка</label>
                <select name="sort">
                    <option value="name" <?php echo $f_sort === 'name' ? 'selected' : ''; ?>>По ФИО</option>
                    <option value="group" <?php echo $f_sort === 'group' ? 'selected' : ''; ?>>По группе</option>
                    <option value="avg" <?php echo $f_sort === 'avg' ? 'selected' : ''; ?>>По среднему баллу</option>
                </select>
            </div>
            <div class="form-group">
                <label>Направление</label>
                <select name="dir">
                    <option value="asc" <?php echo $f_dir === 'asc' ? 'selected' : ''; ?>>По возрастанию</option>
                    <option value="desc" <?php echo $f_dir === 'desc' ? 'selected' : ''; ?>>По убыванию</option>
                </select>
            </div>
            <button type="submit" class="btn">Применить</button>
            <a href="index.php" class="btn btn-reset">Сбросить</a>
        </fieldset>
    </form>

    <?php if (empty($itemsForPage) && empty($errorMessages)): ?>
        <div style="text-align: center; padding: 40px;">
            <h3>По вашему запросу ничего не найдено.</h3>
            <a href="index.php" class="btn" style="margin-top: 15px; display: inline-block; text-decoration: none;">Сбросить фильтры</a>
        </div>
    <?php elseif (!empty($itemsForPage)): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th><a href="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'name', 'dir' => $f_sort === 'name' && $f_dir === 'asc' ? 'desc' : 'asc'])); ?>">ФИО</a></th>
                    <th><a href="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'group', 'dir' => $f_sort === 'group' && $f_dir === 'asc' ? 'desc' : 'asc'])); ?>">Группа</a></th>
                    <th>Специальность</th>
                    <th><a href="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'avg', 'dir' => $f_sort === 'avg' && $f_dir === 'asc' ? 'desc' : 'asc'])); ?>">Ср. балл</a></th>
                    <th>Статус</th>
                    <th>E-mail</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($itemsForPage as $s): ?>
                    <tr class="<?php echo $s['status'] === 'expelled' ? 'expelled' : ''; ?>">
                        <td><?php echo e($s['id']); ?></td>
                        <td><strong><?php echo e($s['name']); ?></strong></td>
                        <td><?php echo e($s['group']); ?></td>
                        <td><?php echo e($s['specialty_code'] . ' ' . $s['specialty_name']); ?></td>
                        <td><?php echo $s['avg'] !== null ? e($s['avg']) : '—'; ?></td>
                        <td><?php echo $s['status'] === 'expelled' ? 'Отчислен' : 'Активен'; ?></td>
                        <td><?php echo $s['email'] !== null ? e($s['email']) : '—'; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="active"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="<?php echo e(buildQuery($i)); ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        <?php endif; ?>

        <div class="footer-info">
            Показано <?php echo count($itemsForPage); ?> из <?php echo e($totalFiltered); ?> студентов. 
            Средний балл по выборке: <?php echo e($globalAvg); ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>