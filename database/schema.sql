-- En Railway, ejecuta este script conectado a la base de datos MySQL del servicio.
-- Para uso local puedes crear primero la base cafe_eliel y luego ejecutar estas tablas.

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  email VARCHAR(191) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_users_email (email)
);

CREATE TABLE IF NOT EXISTS sales (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sale_date DATE NOT NULL,
  customer_name VARCHAR(150) DEFAULT NULL,
  description VARCHAR(255) NOT NULL,
  total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_sales_date (sale_date)
);

INSERT INTO users (first_name, last_name, email, password_hash, role)
VALUES ('Admin', 'Sistema', 'admin@cafeeliel.com', 'admin123', 'admin')
ON DUPLICATE KEY UPDATE email = VALUES(email);
