<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off']);
    session_start();
}

require_once __DIR__ . '/portal.php';

function tokenCsrf(): string
{
    return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
}

function campoCsrf(): string
{
    return '<input type="hidden" name="csrf" value="' . e(tokenCsrf()) . '">';
}

function validarCsrf(): void
{
    if (!is_string($_POST['csrf'] ?? null) || !hash_equals(tokenCsrf(), $_POST['csrf'])) {
        http_response_code(403);
        exit('Sessão do formulário expirada. Volte à página anterior, atualize e tente novamente.');
    }
}

function podePublicar(): bool
{
    return in_array($_SESSION['usuario_perfil'] ?? '', ['admin', 'editor'], true);
}

function podeGerenciarConteudo(array $conteudo): bool
{
    return podePublicar() || ((int) ($conteudo['autor_id'] ?? 0) === (int) ($_SESSION['usuario_id'] ?? 0));
}

function usuarioAutenticado(): bool
{
    return isset($_SESSION['usuario_id']);
}

function exigirLogin(string $paginaLogin = 'login.php'): void
{
    if (!usuarioAutenticado()) {
        header('Location: ' . $paginaLogin);
        exit;
    }
}

function exigirPerfil(array|string $perfisPermitidos, string $paginaLogin = 'login.php'): void
{
    exigirLogin($paginaLogin);

    $perfisPermitidos = (array) $perfisPermitidos;

    if (!in_array($_SESSION['usuario_perfil'] ?? null, $perfisPermitidos, true)) {
        http_response_code(403);
        exit('Acesso não autorizado.');
    }
}
