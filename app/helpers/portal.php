<?php
declare(strict_types=1);
require_once __DIR__ . '/seguranca.php';

function portalUrl(string $path = ''): string
{
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/public/index.php');
    $base = preg_replace('~/(?:app/views|public)/.*$~', '', $script);
    if ($base === $script) {
        $base = rtrim(dirname($script), '/.');
    }
    return $base . '/' . ltrim($path, '/');
}

function textoEntrada(array $dados, string $chave, string $padrao = ''): string
{
    return isset($dados[$chave]) && is_string($dados[$chave]) ? trim($dados[$chave]) : $padrao;
}

function imagemConteudo(?string $caminho): ?string
{
    if (!$caminho || !preg_match('~^uploads/[a-zA-Z0-9_.-]+\\.(?:jpg|jpeg|png|gif)$~i', $caminho)) {
        return null;
    }
    return is_file(__DIR__ . '/../views/admin/' . $caminho)
        ? portalUrl('app/views/admin/' . $caminho) : null;
}

function youtubeValido(string $url): bool
{
    return $url === '' || (strlen($url) <= 255 && filter_var($url, FILTER_VALIDATE_URL)
        && parse_url($url, PHP_URL_SCHEME) === 'https'
        && in_array(strtolower((string) parse_url($url, PHP_URL_HOST)), ['youtube.com', 'www.youtube.com', 'm.youtube.com', 'youtu.be'], true));
}

function slugConteudo(string $titulo): string
{
    $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $titulo) ?: 'publicacao';
    $slug = trim((string) preg_replace('/[^a-z0-9]+/', '-', strtolower($ascii)), '-');
    return substr($slug ?: 'publicacao', 0, 180) . '-' . bin2hex(random_bytes(6));
}
