-- SQL Script to Grant Database Access
-- Run this script on the MySQL server (10.28.98.145) as a user with GRANT privileges
-- You can run this in phpMyAdmin or MySQL command line

-- Option 1: Grant access from any host (most flexible)
CREATE USER IF NOT EXISTS 'admin'@'%' IDENTIFIED BY 'admin@123';
GRANT ALL PRIVILEGES ON mpesa_db.* TO 'admin'@'%';
GRANT ALL PRIVILEGES ON *.* TO 'admin'@'%' WITH GRANT OPTION;
FLUSH PRIVILEGES;

-- Option 2: Grant access from specific hostname (if you know the hostname)
-- Replace 'DESKTOP-C3NVLT3' with your actual hostname
-- CREATE USER IF NOT EXISTS 'admin'@'DESKTOP-C3NVLT3' IDENTIFIED BY 'admin@123';
-- GRANT ALL PRIVILEGES ON mpesa_db.* TO 'admin'@'DESKTOP-C3NVLT3';
-- FLUSH PRIVILEGES;

-- Option 3: Update existing user to allow from any host
-- UPDATE mysql.user SET Host='%' WHERE User='admin' AND Host='localhost';
-- FLUSH PRIVILEGES;

-- Verify the user was created/updated correctly
SELECT User, Host FROM mysql.user WHERE User='admin';

