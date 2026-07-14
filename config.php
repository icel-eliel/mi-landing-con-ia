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
