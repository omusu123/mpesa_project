<?php
// Database configuration
// Reads from environment variables (Render) or uses defaults (local development)
$host = $_ENV['DB_HOST'] ?? '10.28.98.145';
$dbname = $_ENV['DB_NAME'] ?? 'mpesa_db';
$username = $_ENV['DB_USER'] ?? 'admin';
$password = $_ENV['DB_PASS'] ?? 'admin@123';

try {
  // Try to connect directly to the database first
  try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    // If database doesn't exist, try to create it
    if (strpos($e->getMessage(), 'Unknown database') !== false) {
      // Connect to MySQL server (without database)
      $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      
      // Create database if it doesn't exist
      $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
      
      // Now connect to the specific database
      $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } else {
      throw $e; // Re-throw if it's a different error
    }
  }
  
  // Create table if it doesn't exist
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
  
} catch (PDOException $e) {
  die("Database connection failed: " . $e->getMessage() . 
      "<br><br>Please make sure:<br>" .
      "1. MySQL server is running and accessible at $host<br>" .
      "2. Your credentials are correct in db.php<br>" .
      "3. Database '$dbname' exists or you have privileges to create it<br>" .
      "4. Network connectivity to the database server is available");
}
