-- M-Pesa Database Setup
-- Run this script to create the database and table
-- You can either:
-- 1. Import this in phpMyAdmin
-- 2. Run: mysql -h 10.28.98.145 -u admin -p admin@123 < database.sql
-- 3. Or use the setup_db.php script

CREATE DATABASE IF NOT EXISTS mpesa_db;

-- Use the database
USE mpesa_db;

-- Create payments table
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
