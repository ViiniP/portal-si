<?php
/** Resumo: Destruição completa da sessão, cookie de transporte e redirecionamento. **/
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) { session_start(); }
// 1. Limpa todas as variáveis da sessão em memória 
$_SESSION = [];
// 2. Invalida o cookie de sessão no cliente 
if (ini_get("session.use_cookies")) { $params = session_get_cookie_params(); setcookie( session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"] ); }
// 4. Redireciona para o login com indicação de encerramento 
header('Location: login.php?msg=desconectado'); exit();
