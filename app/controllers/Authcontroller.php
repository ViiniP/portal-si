<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/Usuarios.php';

function autenticar(PDO $pdo, string $email, string $senha): bool
{
    $usuario = (new Usuario($pdo))->buscarPorEmail($email);
    if (!$usuario || !password_verify($senha, $usuario['senha_hash'])) {
        return false;
    }
    session_regenerate_id(true);
    $_SESSION['usuario_id'] = (int) $usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
    $_SESSION['usuario_email'] = $usuario['email'];
    $_SESSION['usuario_perfil'] = $usuario['perfil'];
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return true;
}
