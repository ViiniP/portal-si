<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../config/database.php';
$pdo->beginTransaction();
try {
    $pdo->exec(file_get_contents(__DIR__ . '/migrations/20261119_imagens.sql'));
    $pdo->commit();
    echo "Armazenamento persistente de imagens disponível.\n";
} catch (Throwable $error) {
    $pdo->rollBack();
    fwrite(STDERR, "Falha ao preparar armazenamento de imagens.\n");
    exit(1);
}
