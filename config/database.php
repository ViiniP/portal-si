<?php
declare(strict_types=1);

date_default_timezone_set('America/Sao_Paulo');
$localConfig = [];
// Um DB_HOST explícito seleciona um ambiente independente da configuração local.
if (getenv('DB_HOST') === false && is_file(__DIR__ . '/database.local.php')) {
    $localConfig = require __DIR__ . '/database.local.php';
}
$dbSetting = static function (string $key, string $default = '') use ($localConfig): string {
    $value = getenv($key);
    return $value !== false ? $value : (string) ($localConfig[$key] ?? $default);
};
try {
    $host = $dbSetting('DB_HOST', 'localhost');
    $port = $dbSetting('DB_PORT', '5432');
    $dbname = $dbSetting('DB_NAME', 'portal_si');
    $sslmode = $dbSetting('DB_SSLMODE', 'prefer');
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;connect_timeout=10;sslmode=$sslmode";
    $sslrootcert = str_replace('\\', '/', $dbSetting('DB_SSLROOTCERT'));
    if ($sslrootcert !== '') $dsn .= ';sslrootcert=' . $sslrootcert;
    $pdo = new PDO($dsn,
        $dbSetting('DB_USER', 'postgres'), $dbSetting('DB_PASSWORD', 'postgres'), [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
} catch (PDOException $exception) {
    error_log('Portal SI: ' . $exception->getMessage());
    http_response_code(503);
    require __DIR__ . '/../app/views/layout/indisponivel.php';
    exit(PHP_SAPI === 'cli' ? 1 : 0);
}
