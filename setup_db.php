<?php
/**
 * Database Setup Script
 * Run this file once to set up your database
 * Visit: http://localhost/mpesa_project/setup_db.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = '10.28.98.145';
$dbname = 'mpesa_db';
$username = 'admin';
$password = 'admin@123';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card p-4">
            <h2 class="mb-4">M-Pesa Database Setup</h2>
            
            <?php
            try {
                // Connect to MySQL server
                $pdo = new PDO("mysql:host=$host", $username, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                echo "<div class='alert alert-success'>✅ Connected to MySQL server successfully</div>";
                
                // Create database
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
                echo "<div class='alert alert-success'>✅ Database '$dbname' created/verified</div>";
                
                // Connect to the database
                $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Create table
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
                echo "<div class='alert alert-success'>✅ Table 'payments' created/verified</div>";
                
                // Check if table has data
                $stmt = $pdo->query("SELECT COUNT(*) FROM payments");
                $count = $stmt->fetchColumn();
                echo "<div class='alert alert-info'>ℹ️ Payments table contains $count record(s)</div>";
                
                echo "<div class='alert alert-success mt-4'><strong>Setup Complete!</strong><br>";
                echo "Your database is ready to use. <a href='index.php' class='alert-link'>Go to Payment Form</a></div>";
                
            } catch (PDOException $e) {
                echo "<div class='alert alert-danger'>❌ Error: " . $e->getMessage() . "</div>";
                echo "<div class='alert alert-warning mt-3'>";
                echo "<strong>Common Issues:</strong><br>";
                echo "1. Make sure MySQL server is running and accessible at $host<br>";
                echo "2. Verify your database credentials in setup_db.php<br>";
                echo "3. Check network connectivity to the database server<br>";
                echo "4. Ensure the database server allows connections from your IP<br>";
                echo "</div>";
            }
            ?>
            
            <div class="mt-4">
                <a href="test_db.php" class="btn btn-primary">Test Database Connection</a>
                <a href="index.php" class="btn btn-secondary">Go to Payment Form</a>
            </div>
        </div>
    </div>
</body>
</html>

