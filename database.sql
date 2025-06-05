CREATE DATABASE IF NOT EXISTS mpesa;
USE mpesa;

CREATE TABLE IF NOT EXISTS payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  phone VARCHAR(20),
  amount DECIMAL(10, 2),
  mpesa_code VARCHAR(100),
  result_code INT,
  result_desc TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
