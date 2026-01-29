<?php
/**
 * One-time migration: add pro_pdf column to product_details.
 * Run once in browser: http://your-site/vedantlights/migrate_pro_pdf.php
 * Or from CLI: php migrate_pro_pdf.php
 * Delete this file after running.
 */

$message = '';
$done = false;

if (php_sapi_name() !== 'cli') {
    header('Content-Type: text/html; charset=utf-8');
}

try {
    define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
    chdir(FCPATH);

    if (!file_exists(FCPATH . 'app/Config/Paths.php')) {
        throw new Exception('Paths.php not found. Run this from the project root.');
    }

    require FCPATH . 'app/Config/Paths.php';
    $paths = new Config\Paths();
    require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';
    $app = Config\Services::codeigniter();
    $app->initialize();

    $db = \Config\Database::connect();
    $dbName = $db->getDatabase();

    // Check if pro_pdf column already exists
    $exists = false;
    try {
        $res = $db->query(
            "SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'product_details' AND COLUMN_NAME = 'pro_pdf' LIMIT 1",
            [$dbName]
        );
        $row = $res->getRow();
        $exists = (bool) $row;
    } catch (Throwable $e) {
        // Fallback: try ALTER; if column exists we get duplicate error
    }

    if ($exists) {
        $message = 'Column pro_pdf already exists. No change made.';
        $done = true;
    } else {
        try {
            $db->query('ALTER TABLE `product_details` ADD COLUMN `pro_pdf` varchar(255) DEFAULT NULL AFTER `pro_img`');
            $message = 'Column pro_pdf added successfully.';
        } catch (Throwable $e) {
            if (strpos($e->getMessage(), 'Duplicate column') !== false) {
                $message = 'Column pro_pdf already exists. No change made.';
            } else {
                throw $e;
            }
        }
        $done = true;
    }
} catch (Throwable $e) {
    $message = 'Error: ' . $e->getMessage();
}

if (php_sapi_name() === 'cli') {
    echo $message . PHP_EOL;
    exit($done ? 0 : 1);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>PDF column migration</title>
</head>
<body>
    <p><?= htmlspecialchars($message) ?></p>
    <?php if ($done): ?>
    <p><strong>You can delete this file (migrate_pro_pdf.php) now.</strong></p>
    <?php endif; ?>
</body>
</html>
