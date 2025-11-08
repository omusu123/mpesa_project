# Database Connection Troubleshooting Guide

## Current Status
❌ **Connection Failed** - Access denied for user 'admin'@'DESKTOP-C3NVLT3'

## Connection Details
- **Host:** 10.28.98.145
- **Database:** mpesa_db
- **Username:** admin
- **Password:** admin@123
- **Your Hostname:** DESKTOP-C3NVLT3

## Problem Diagnosis
The MySQL server is reachable, but the user 'admin' doesn't have permission to connect from your hostname (DESKTOP-C3NVLT3).

## Solutions

### Solution 1: Grant Access from Any Host (Recommended)
Run these commands on the MySQL server (10.28.98.145) in phpMyAdmin or MySQL command line:

```sql
CREATE USER IF NOT EXISTS 'admin'@'%' IDENTIFIED BY 'admin@123';
GRANT ALL PRIVILEGES ON mpesa_db.* TO 'admin'@'%';
GRANT CREATE ON *.* TO 'admin'@'%';
FLUSH PRIVILEGES;
```

### Solution 2: Grant Access from Your Specific Hostname
If you prefer to restrict access to your specific hostname:

```sql
CREATE USER IF NOT EXISTS 'admin'@'DESKTOP-C3NVLT3' IDENTIFIED BY 'admin@123';
GRANT ALL PRIVILEGES ON mpesa_db.* TO 'admin'@'DESKTOP-C3NVLT3';
GRANT CREATE ON *.* TO 'admin'@'DESKTOP-C3NVLT3';
FLUSH PRIVILEGES;
```

### Solution 3: Update Existing User
If the user already exists but with host restrictions:

```sql
-- Check existing users
SELECT User, Host FROM mysql.user WHERE User='admin';

-- Update to allow from any host
UPDATE mysql.user SET Host='%' WHERE User='admin';
FLUSH PRIVILEGES;
```

### Solution 4: Create Database if It Doesn't Exist
If the database doesn't exist yet, create it:

```sql
CREATE DATABASE IF NOT EXISTS mpesa_db;
```

## How to Run These Commands

### Option A: Using phpMyAdmin
1. Open phpMyAdmin: http://10.28.98.145/phpmyadmin (or the appropriate URL)
2. Login with a user that has GRANT privileges (usually 'root')
3. Click on "SQL" tab
4. Paste the SQL commands from Solution 1
5. Click "Go" to execute

### Option B: Using MySQL Command Line
1. SSH into the server at 10.28.98.145 (if you have access)
2. Run: `mysql -u root -p`
3. Enter the root password
4. Execute the SQL commands

### Option C: Contact Database Administrator
If you don't have access to run these commands, contact your database administrator and provide them with:
- The SQL script: `grant_access.sql`
- Your hostname: DESKTOP-C3NVLT3
- The need to grant 'admin' user access from your host

## Verify Connection After Fix

After running the SQL commands, test the connection again:

```bash
php test_connection_cli.php
```

Or visit in browser:
- http://localhost:8000/test_db.php
- http://localhost:8000/setup_db.php

## Additional Checks

1. **Verify Password**: Double-check that the password is exactly `admin@123`
2. **Check MySQL Configuration**: Ensure MySQL is configured to accept remote connections
3. **Firewall**: Verify port 3306 is open between your machine and the server
4. **Network**: Test connectivity: `ping 10.28.98.145` or `telnet 10.28.98.145 3306`

## Files Created for Testing

- `test_connection_cli.php` - Command-line connection test
- `diagnose_connection.php` - Detailed diagnostics
- `grant_access.sql` - SQL script to grant access
- `test_db.php` - Web-based connection test (access via browser)

## Next Steps

1. Run the SQL commands on the MySQL server to grant access
2. Test the connection using `php test_connection_cli.php`
3. If successful, proceed to use the application

