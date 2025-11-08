<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test database connection parameters
$host = '10.28.98.145';
$dbname = 'mpesa_db';
$username = 'admin';
$password = 'admin@123';

echo "<h2>Database Connection Test</h2>";

try {
    // Test connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<div style='color: green;'>✅ Successfully connected to the database server.</div><br>";
    
    // List all databases (to verify MySQL is running)
    $databases = $pdo->query('SHOW DATABASES');
    $dbExists = false;
    
    echo "<strong>Available databases:</strong><br>";
    while ($row = $databases->fetch(PDO::FETCH_NUM)) {
        echo "- " . $row[0] . "<br>";
        if (strtolower($row[0]) === 'mpesa_db') {
            $dbExists = true;
        }
    }
    
    if (!$dbExists) {
        throw new PDOException("Database 'mpesa_db' not found. Please create it first.");
    }
    
    // Test query to check if the payments table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'payments'");
    if ($stmt->rowCount() > 0) {
        $count = $pdo->query("SELECT COUNT(*) FROM payments")->fetchColumn();
        echo "<div style='color: green;'>✅ Found 'payments' table with $count records.</div>";
    } else {
        echo "<div style='color: orange;'>⚠️ 'payments' table not found. You'll need to import the database.sql file.</div>";
    }
    
} catch (PDOException $e) {
    echo "<div style='color: red;'>❌ Database Error: " . $e->getMessage() . "</div>";
    
    // Detailed debug information
    echo "<div style='margin: 20px; padding: 10px; background: #f8f8f8; border: 1px solid #ddd;'>";
    echo "<h3>Debug Information</h3>";
    echo "<strong>Connection Details:</strong><br>";
    echo "- Host: $host<br>";
    echo "- Database: $dbname<br>";
    echo "- Username: $username<br>";
    echo "- Password: " . (empty($password) ? '(empty)' : '*****') . "<br><br>";
    
    echo "<strong>Common Issues:</strong><br>";
    echo "1. <strong>MySQL Service Not Running:</strong><br>";
    echo "   - Open XAMPP Control Panel<br>";
    echo "   - Make sure MySQL is running (green 'Running' status)<br><br>";
    
    echo "2. <strong>Database Doesn't Exist:</strong><br>";
    echo "   - Open phpMyAdmin and connect to host: $host<br>";
    echo "   - Click 'New' in the left sidebar<br>";
    echo "   - Enter 'mpesa_db' as the database name and click 'Create'<br><br>";
    
    echo "3. <strong>Incorrect Credentials:</strong><br>";
    echo "   - Verify your database credentials in db.php<br>";
    echo "   - Host: $host<br>";
    echo "   - Database: $dbname<br>";
    echo "   - Username: $username<br><br>";
    
    // Specific error handling
    if (strpos($e->getMessage(), 'Unknown database') !== false) {
        echo "<div style='color: orange;'>⚠️ The database 'mpesa_db' doesn't exist. Please create it first.</div>";
    } elseif (strpos($e->getMessage(), 'Access denied') !== false) {
        echo "<div style='color: orange;'>⚠️ Access denied. Please check your username and password in db.php</div>";
    } elseif (strpos($e->getMessage(), 'could not find driver') !== false) {
        echo "<div style='color: orange;'>⚠️ PDO MySQL driver not found. Make sure PHP MySQL PDO extension is enabled in php.ini</div>";
    } elseif (strpos($e->getMessage(), 'Connection refused') !== false) {
        echo "<div style='color: orange;'>⚠️ Could not connect to MySQL server. Is MySQL running?</div>";
    }
    echo "</div>";
}
?>
