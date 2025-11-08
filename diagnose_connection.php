<?php
/**
 * Database Connection Diagnostics
 */

$host = '10.28.98.145';
$dbname = 'mpesa_db';
$username = 'admin';
$password = 'admin@123';

echo "=== Database Connection Diagnostics ===\n\n";

// Test 1: Basic connection test
echo "Test 1: Basic MySQL Connection\n";
echo "Host: $host\n";
echo "Username: $username\n";
echo "Password: " . str_repeat('*', strlen($password)) . " (length: " . strlen($password) . ")\n\n";

try {
    $pdo = new PDO("mysql:host=$host", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5
    ]);
    echo "✓ Connection successful!\n\n";
    
    // Get MySQL version
    $version = $pdo->query('SELECT VERSION()')->fetchColumn();
    echo "MySQL Version: $version\n\n";
    
    // List databases
    echo "Available databases:\n";
    $databases = $pdo->query('SHOW DATABASES');
    while ($row = $databases->fetch(PDO::FETCH_NUM)) {
        echo "  - " . $row[0] . "\n";
    }
    
} catch (PDOException $e) {
    echo "✗ Connection failed!\n\n";
    echo "Error Code: " . $e->getCode() . "\n";
    echo "Error Message: " . $e->getMessage() . "\n\n";
    
    $errorMsg = $e->getMessage();
    
    if (strpos($errorMsg, 'Access denied') !== false) {
        echo "=== DIAGNOSIS: Access Denied ===\n\n";
        echo "Possible causes:\n";
        echo "1. Incorrect password - Verify the password is exactly: admin@123\n";
        echo "2. User doesn't exist - Verify 'admin' user exists on MySQL server\n";
        echo "3. Host restriction - The 'admin' user might not have permission from your IP/host\n";
        echo "   Your hostname: " . gethostname() . "\n";
        echo "   Your IP might need to be granted access\n\n";
        echo "Solutions:\n";
        echo "- Verify credentials with your database administrator\n";
        echo "- Check if user needs to be created: CREATE USER 'admin'@'%' IDENTIFIED BY 'admin@123';\n";
        echo "- Grant privileges: GRANT ALL PRIVILEGES ON *.* TO 'admin'@'%' WITH GRANT OPTION;\n";
        echo "- Or grant for specific host: GRANT ALL PRIVILEGES ON mpesa_db.* TO 'admin'@'your-ip' IDENTIFIED BY 'admin@123';\n";
        echo "- Then run: FLUSH PRIVILEGES;\n";
    } elseif (strpos($errorMsg, 'Connection refused') !== false || strpos($errorMsg, 'No connection') !== false) {
        echo "=== DIAGNOSIS: Connection Refused ===\n\n";
        echo "Possible causes:\n";
        echo "1. MySQL server is not running on $host\n";
        echo "2. Firewall is blocking port 3306\n";
        echo "3. MySQL is not configured to accept remote connections\n";
        echo "4. Wrong hostname/IP address\n\n";
        echo "Solutions:\n";
        echo "- Verify MySQL server is running\n";
        echo "- Check firewall settings\n";
        echo "- Verify MySQL bind-address in my.cnf (should be 0.0.0.0 or the server IP)\n";
        echo "- Test connectivity: ping $host or telnet $host 3306\n";
    } elseif (strpos($errorMsg, 'Unknown host') !== false) {
        echo "=== DIAGNOSIS: Unknown Host ===\n\n";
        echo "The hostname $host cannot be resolved.\n";
        echo "Verify the IP address is correct.\n";
    }
}

echo "\n=== End of Diagnostics ===\n";

