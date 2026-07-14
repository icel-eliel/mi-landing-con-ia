<?php
session_start();

define('APP_ROOT', __DIR__);

function getDbConnection(): PDO
{
    $host = getenv('DB_HOST') ?: 'hayabusa.proxy.rlwy.net';
    $port = getenv('DB_PORT') ?: '49731';
    $dbname = getenv('DB_NAME') ?: 'railway';
    $charset = 'utf8mb4';


    // Leer variables con fallback entre convenciones comunes (DB_*, MYSQL_*, DATABASE_URL)
    $host = getenv('DB_HOST') ?: getenv('MYSQLHOST') ?: getenv('MYSQL_HOST') ?: null;
    $port = getenv('DB_PORT') ?: getenv('MYSQLPORT') ?: getenv('MYSQL_PORT') ?: '3306';
    $dbname = getenv('DB_NAME') ?: getenv('MYSQLDATABASE') ?: getenv('MYSQL_DATABASE') ?: 'cafe_eliel';
    $user = getenv('DB_USERNAME') ?: getenv('MYSQLUSER') ?: getenv('MYSQL_USER') ?: 'root';
    $pass = getenv('DB_PASSWORD') ?: getenv('MYSQLPASSWORD') ?: getenv('MYSQL_ROOT_PASSWORD') ?: '';
    $charset = 'utf8mb4';

    // Si el proveedor entrega una URL completa (ej. mysql://user:pass@host:port/db), parsearla
    $full = getenv('MYSQL_URL') ?: getenv('DATABASE_URL') ?: null;
    if ($full && !$host) {
        $parts = parse_url($full);
        if ($parts) {
            $host = $parts['host'] ?? $host;
            $port = $parts['port'] ?? $port;
            $user = $parts['user'] ?? $user;
            $pass = $parts['pass'] ?? $pass;
            $path = $parts['path'] ?? null;
            if ($path) {
                $dbname = ltrim($path, '/');
            }
        }
    }

    if (!$host || !$dbname || !$user) {
        throw new RuntimeException('Faltan credenciales de BD en las variables de entorno.');
    }

    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        error_log('DB connect error: ' . $e->getMessage());
        throw new RuntimeException('No fue posible conectar con la base de datos.');
    }
}

function isLoggedIn(): bool
{
    return !empty($_SESSION['user_id']);
}

function isAdmin(): bool
{
    return isLoggedIn() && ($_SESSION['role'] ?? '') === 'admin';
}

function requireAuth(?string $requiredRole = null): void
{
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }

    if ($requiredRole === 'admin' && !isAdmin()) {
        header('Location: index.php');
        exit;
    }
}

function getDisplayName(): string
{
    $name = trim($_GET['name'] ?? '');

    if ($name !== '') {
        return htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    }

    return isset($_SESSION['first_name'])
        ? htmlspecialchars($_SESSION['first_name'], ENT_QUOTES, 'UTF-8')
        : 'visitante';
}

function verifyPassword(string $password, string $storedHash): bool
{
    $info = password_get_info($storedHash);

    if (($info['algo'] ?? null) !== 0) {
        return password_verify($password, $storedHash);
    }

    return hash_equals($storedHash, $password);
}
