<?php
require 'db.php';

echo "=== Database Verification ===\n\n";

try {
    // Get current database
    $stmt = $pdo->query('SELECT DATABASE() as db');
    $db = $stmt->fetch();
    echo "✓ Connected to database: " . $db['db'] . "\n\n";
    
    // List all tables
    $stmt = $pdo->query('SHOW TABLES');
    echo "Tables in database:\n";
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        echo "  - " . $row[0] . "\n";
    }
    echo "\n";
    
    // Describe payments table
    $stmt = $pdo->query('DESCRIBE payments');
    echo "Payments table structure:\n";
    echo str_repeat('-', 60) . "\n";
    printf("%-20s %-20s %-10s %s\n", "Field", "Type", "Null", "Key");
    echo str_repeat('-', 60) . "\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        printf("%-20s %-20s %-10s %s\n", 
            $row['Field'], 
            $row['Type'], 
            $row['Null'], 
            $row['Key']
        );
    }
    echo str_repeat('-', 60) . "\n\n";
    
    // Count records
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM payments');
    $count = $stmt->fetch();
    echo "Records in payments table: " . $count['count'] . "\n\n";
    
    echo "✓ Database is fully configured and ready to use!\n";
    
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

