<?php
session_start();

define('APP_ROOT', __DIR__);

function getDbConnection(): PDO
{
    $host = getenv('DB_HOST') ?: 'localhost';
    $port = getenv('DB_PORT') ?: '3306';
    $dbname = getenv('DB_NAME') ?: 'cafe_eliel';
    $user = getenv('DB_USERNAME') ?: 'root';
    $pass = getenv('DB_PASSWORD') ?: '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    return new PDO($dsn, $user, $pass, $options);
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
