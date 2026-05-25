<?php
// Database connection configuration
function env(string $key, string $default): string {
    $value = getenv($key);
    if ($value === false || trim((string) $value) === '') {
        return $default;
    }

    return $value;
}

$host = env('DB_HOST', '127.0.0.1');
$db   = env('DB_NAME', 'quiz_app');
$user = env('DB_USER', 'root');
$pass = env('DB_PASS', '');
$charset = env('DB_CHARSET', 'utf8mb4');

$rootUrl = '';
if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], '8000') !== false) {
    $rootUrl = '';
} else {
    $rootUrl = '/MPI';
}
define('BASE_URL', $rootUrl);

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    echo 'Database connection failed: ' . htmlspecialchars($e->getMessage());
    exit;
}
