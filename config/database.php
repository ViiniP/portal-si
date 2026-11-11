<?php
declare(strict_types=1);

date_default_timezone_set('America/Sao_Paulo');
try {
    $host = getenv('DB_HOST') ?: 'localhost';
    $port = getenv('DB_PORT') ?: '5432';
    $dbname = getenv('DB_NAME') ?: 'portal_si';
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname;connect_timeout=5",
        getenv('DB_USER') ?: 'postgres', getenv('DB_PASSWORD') ?: 'postgres', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
} catch (PDOException $exception) {
    error_log('Portal SI: ' . $exception->getMessage());
    http_response_code(503);
    require __DIR__ . '/../app/views/layout/indisponivel.php';
    exit;
}
