<?php
require_once __DIR__ . '/auth.php';

define('APP_ROOT', __DIR__);

function getDbConnection(): PDO
{
    $databaseUrl = getenv('MYSQL_URL') ?: getenv('DATABASE_URL') ?: '';
    $parsedUrl = $databaseUrl !== '' ? parse_url($databaseUrl) : [];
    $parts = is_array($parsedUrl) ? $parsedUrl : [];

    $host = getenv('DB_HOST')
        ?: getenv('MYSQLHOST')
        ?: getenv('MYSQL_HOST')
        ?: ($parts['host'] ?? 'localhost');

    $port = getenv('DB_PORT')
        ?: getenv('MYSQLPORT')
        ?: getenv('MYSQL_PORT')
        ?: ($parts['port'] ?? '3306');

    $dbname = getenv('DB_NAME')
        ?: getenv('MYSQLDATABASE')
        ?: getenv('MYSQL_DATABASE')
        ?: (isset($parts['path']) ? ltrim($parts['path'], '/') : 'cafe_eliel');

    $user = getenv('DB_USERNAME')
        ?: getenv('DB_USER')
        ?: getenv('MYSQLUSER')
        ?: getenv('MYSQL_USER')
        ?: ($parts['user'] ?? 'root');

    $pass = getenv('DB_PASSWORD')
        ?: getenv('MYSQLPASSWORD')
        ?: getenv('MYSQL_PASSWORD')
        ?: getenv('MYSQL_ROOT_PASSWORD')
        ?: ($parts['pass'] ?? '');

    if (!$host || !$dbname || !$user) {
        throw new RuntimeException('Faltan credenciales de BD en las variables de entorno.');
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        urldecode((string) $host),
        urldecode((string) $port),
        urldecode((string) $dbname)
    );

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
        return new PDO($dsn, urldecode((string) $user), urldecode((string) $pass), $options);
    } catch (PDOException $e) {
        error_log('DB connect error: ' . $e->getMessage());
        throw new RuntimeException('No fue posible conectar con la base de datos.', 0, $e);
    }
}

function ensureDemoData(PDO $pdo): void
{
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            email VARCHAR(191) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_users_email (email)
        )"
    );

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS sales (
            id INT AUTO_INCREMENT PRIMARY KEY,
            sale_date DATE NOT NULL,
            customer_name VARCHAR(150) DEFAULT NULL,
            description VARCHAR(255) NOT NULL,
            total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_sales_date (sale_date)
        )"
    );

    $passwordHash = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare(
        "INSERT INTO users (first_name, last_name, email, password_hash, role)
         VALUES ('Admin', 'Sistema', 'admin@cafeeliel.com', ?, 'admin')
         ON DUPLICATE KEY UPDATE
            first_name = VALUES(first_name),
            last_name = VALUES(last_name),
            password_hash = VALUES(password_hash),
            role = VALUES(role)"
    );
    $stmt->execute([$passwordHash]);
}
