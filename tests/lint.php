<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
$root = dirname(__DIR__);
$paths = [$root . '/index.php'];
foreach (['app', 'config', 'public', 'tests'] as $dir) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/' . $dir, FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        if ($file->getExtension() === 'php' && !str_contains($file->getPathname(), '.runtime')) $paths[] = $file->getPathname();
    }
}
$errors = 0;
foreach ($paths as $path) {
    passthru(escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($path), $code);
    if ($code !== 0) $errors++;
}
echo count($paths) . " arquivos PHP; $errors falhas de sintaxe.\n";
exit($errors ? 1 : 0);
