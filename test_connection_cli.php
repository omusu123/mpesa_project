<?php
/**
 * CLI Database Connection Test
 * Run this from command line: php test_connection_cli.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = '10.28.98.145';
$dbname = 'mpesa_db';
$username = 'admin';
$password = 'admin@123';

echo "========================================\n";
echo "M-Pesa Database Connection Test\n";
echo "========================================\n\n";

echo "Connection Details:\n";
echo "  Host: $host\n";
echo "  Database: $dbname\n";
echo "  Username: $username\n";
echo "  Password: " . str_repeat('*', strlen($password)) . "\n\n";

echo "Testing connection...\n";
echo str_repeat('-', 40) . "\n";

try {
    // Test connection to MySQL server
    echo "1. Connecting to MySQL server... ";
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ SUCCESS\n";
    
    // Check if database exists
    echo "2. Checking if database '$dbname' exists... ";
    $databases = $pdo->query('SHOW DATABASES');
    $dbExists = false;
    while ($row = $databases->fetch(PDO::FETCH_NUM)) {
        if (strtolower($row[0]) === strtolower($dbname)) {
            $dbExists = true;
            break;
        }
    }
    
    if ($dbExists) {
        echo "✓ EXISTS\n";
    } else {
        echo "✗ NOT FOUND\n";
        echo "3. Creating database '$dbname'... ";
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
        echo "✓ CREATED\n";
    }
    
    // Connect to the database
    echo "4. Connecting to database '$dbname'... ";
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    echo "✓ SUCCESS\n";
    
    // Check if payments table exists
    echo "5. Checking if 'payments' table exists... ";
    $stmt = $pdo->query("SHOW TABLES LIKE 'payments'");
    if ($stmt->rowCount() > 0) {
        echo "✓ EXISTS\n";
        
        // Count records
        $count = $pdo->query("SELECT COUNT(*) FROM payments")->fetchColumn();
        echo "6. Records in payments table: $count\n";
    } else {
        echo "✗ NOT FOUND\n";
        echo "7. Creating 'payments' table... ";
        
        $createTableQuery = "
            CREATE TABLE IF NOT EXISTS payments (
              id INT AUTO_INCREMENT PRIMARY KEY,
              phone VARCHAR(20) NOT NULL,
              amount DECIMAL(10, 2) NOT NULL,
              mpesa_code VARCHAR(100),
              result_code INT,
              result_desc TEXT,
              merchant_request_id VARCHAR(100),
              checkout_request_id VARCHAR(100),
              created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
              INDEX idx_phone (phone),
              INDEX idx_mpesa_code (mpesa_code),
              INDEX idx_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        $pdo->exec($createTableQuery);
        echo "✓ CREATED\n";
    }
    
    echo "\n" . str_repeat('=', 40) . "\n";
    echo "✓ DATABASE CONNECTION SUCCESSFUL!\n";
    echo str_repeat('=', 40) . "\n";
    echo "\nDatabase is ready to use.\n";
    echo "You can now:\n";
    echo "  1. Visit http://localhost:8000/index.php to use the payment form\n";
    echo "  2. Visit http://localhost:8000/setup_db.php for web-based setup\n";
    echo "  3. Visit http://localhost:8000/test_db.php for web-based test\n";
    
} catch (PDOException $e) {
    echo "✗ FAILED\n\n";
    echo str_repeat('=', 40) . "\n";
    echo "ERROR: " . $e->getMessage() . "\n";
    echo str_repeat('=', 40) . "\n\n";
    
    echo "Troubleshooting:\n";
    echo "  1. Verify MySQL server is running at $host\n";
    echo "  2. Check if port 3306 is accessible\n";
    echo "  3. Verify credentials are correct\n";
    echo "  4. Check if 'admin' user has CREATE DATABASE privileges\n";
    echo "  5. Verify network connectivity to $host\n";
    
    exit(1);
}

