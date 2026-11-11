<?php
require_once __DIR__ . '/../../helpers/auth.php';
if (usuarioAutenticado()) {
    header('Location: index.php');
    exit;
}
$erro = '';
$email = textoEntrada($_POST, 'email');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarCsrf();
    require_once __DIR__ . '/../../../config/database.php';
    require_once __DIR__ . '/../../controllers/Authcontroller.php';
    $senha = is_string($_POST['senha'] ?? null) ? $_POST['senha'] : '';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $senha === '') {
        $erro = 'Informe seu e-mail e sua senha para continuar.';
    } elseif (autenticar($pdo, $email, $senha)) {
        header('Location: index.php');
        exit;
    } else {
        $erro = 'E-mail ou senha inválidos.';
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Entrar | Portal SI</title><link rel="stylesheet" href="../../../public/assets/css/auth.css"></head>
<body><main class="auth-layout">
<section class="auth-intro"><a class="brand" href="<?= e(portalUrl('public/index.php')) ?>"><span>SI</span> Portal Acadêmico</a><div><p class="eyebrow">SISTEMAS DE INFORMAÇÃO · ESUCRI</p><h1>Conhecimento que conecta.</h1><p>Projetos, pesquisas e experiências da nossa comunidade em um só lugar.</p></div><p class="intro-footer">Aprender. Compartilhar. Transformar.</p></section>
<section class="auth-form"><div class="auth-card"><p class="eyebrow">BEM-VINDO DE VOLTA</p><h2>Acesse o portal</h2><p class="muted">Entre para acompanhar e compartilhar conhecimento.</p>
<?php if ($erro): ?><div class="alert error" role="alert"><?= e($erro) ?></div><?php endif; ?>
<?php if (textoEntrada($_GET, 'sucesso') === 'cadastrado'): ?><div class="alert success" role="status">Conta criada! Entre com seu e-mail e sua senha.</div><?php endif; ?>
<?php if (textoEntrada($_GET, 'msg') === 'desconectado'): ?><div class="alert success" role="status">Você saiu da sua conta com segurança.</div><?php endif; ?>
<form method="post" action="login.php"><?= campoCsrf() ?>
<label for="email">E-mail</label><input type="email" name="email" id="email" autocomplete="username" maxlength="160" required value="<?= e($email) ?>" placeholder="voce@instituicao.com">
<label for="senha">Senha</label><input type="password" name="senha" id="senha" autocomplete="current-password" required placeholder="Digite sua senha">
<button type="submit">Entrar no portal <span aria-hidden="true">→</span></button>
</form><p class="form-footer">Ainda não tem uma conta? <a href="cadastro_usuario.php">Criar conta</a></p><a class="back-link" href="<?= e(portalUrl('public/index.php')) ?>">← Voltar ao portal</a></div></section>
</main></body></html>
