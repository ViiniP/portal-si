<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
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

function redirecionarAposLogin(string $paginaInicial): void
{
    $destino = ($_SESSION['usuario_perfil'] ?? null) === 'admin'
        ? 'dashboard.php'
        : $paginaInicial;

    header('Location: ' . $destino);
    exit;
}

function exigirPerfil(
    array|string $perfisPermitidos,
    string $paginaLogin = 'login.php',
    ?string $paginaSemPermissao = null
): void
{
    exigirLogin($paginaLogin);

    $perfisPermitidos = (array) $perfisPermitidos;

    if (!in_array($_SESSION['usuario_perfil'] ?? null, $perfisPermitidos, true)) {
        if ($paginaSemPermissao !== null) {
            header('Location: ' . $paginaSemPermissao);
            exit;
        }

        http_response_code(403);
        exit('Acesso não autorizado.');
    }
}
