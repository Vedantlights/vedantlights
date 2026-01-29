<?php
/**
 * One-time migration: add product_pdf_details table for multiple PDFs per product.
 * Run once in browser: http://your-site/vedantlights/migrate_product_pdfs.php
 * Or from CLI: php migrate_product_pdfs.php
 */

// Minimal CodeIgniter bootstrap for database access
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
$pathsConfig = FCPATH . 'app/Config/Paths.php';

if (!is_file($pathsConfig)) {
    // Fallback: direct database connection
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $dbname = 'vedantlights_db';
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check if table already exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'product_pdf_details'");
        if ($stmt->rowCount() > 0) {
            $message = 'Table product_pdf_details already exists. No change made.';
        } else {
            // Create the table
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS `product_pdf_details` (
                  `pdf_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                  `pro_id` int(11) unsigned NOT NULL,
                  `pdf_name` varchar(255) NOT NULL,
                  `pdf_file` varchar(255) NOT NULL,
                  PRIMARY KEY (`pdf_id`),
                  KEY `pro_id` (`pro_id`),
                  CONSTRAINT `product_pdf_pro_id_fk` FOREIGN KEY (`pro_id`) REFERENCES `product_details` (`pro_id`) ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci
            ");
            $message = 'Table product_pdf_details created successfully!';
        }
    } catch (PDOException $e) {
        $message = 'Database error: ' . $e->getMessage();
    }
} else {
    // Use CodeIgniter's database
    require $pathsConfig;
    $paths = new Config\Paths();
    require $paths->systemDirectory . '/bootstrap.php';
    $app = \Config\Services::codeigniter();
    $app->initialize();
    
    $db = \Config\Database::connect();
    
    try {
        // Check if table already exists
        $result = $db->query("SHOW TABLES LIKE 'product_pdf_details'");
        if ($result->getNumRows() > 0) {
            $message = 'Table product_pdf_details already exists. No change made.';
        } else {
            // Create the table
            $db->query("
                CREATE TABLE IF NOT EXISTS `product_pdf_details` (
                  `pdf_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                  `pro_id` int(11) unsigned NOT NULL,
                  `pdf_name` varchar(255) NOT NULL,
                  `pdf_file` varchar(255) NOT NULL,
                  PRIMARY KEY (`pdf_id`),
                  KEY `pro_id` (`pro_id`),
                  CONSTRAINT `product_pdf_pro_id_fk` FOREIGN KEY (`pro_id`) REFERENCES `product_details` (`pro_id`) ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci
            ");
            $message = 'Table product_pdf_details created successfully!';
        }
    } catch (\Exception $e) {
        $message = 'Database error: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Product PDFs Table Migration</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Product PDFs Table Migration</h1>
    <p class="<?php echo strpos($message, 'error') !== false ? 'error' : 'success'; ?>">
        <?php echo htmlspecialchars($message); ?>
    </p>
    
    <h2>New Table Structure:</h2>
    <pre>
CREATE TABLE `product_pdf_details` (
  `pdf_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pro_id` int(11) unsigned NOT NULL,      -- Links to product_details
  `pdf_name` varchar(255) NOT NULL,        -- Display name (like "Datasheet", "Manual")
  `pdf_file` varchar(255) NOT NULL,        -- Filename stored in uploads/Product/
  PRIMARY KEY (`pdf_id`),
  FOREIGN KEY (`pro_id`) REFERENCES `product_details` (`pro_id`) ON DELETE CASCADE
);</pre>
    
    <p><strong>You can delete this file (migrate_product_pdfs.php) after running it.</strong></p>
</body>
</html>
