<?php
require_once __DIR__ . '/../../helpers/auth.php';
$mensagensErro = [];
$nome = textoEntrada($_POST, 'nome');
$email = strtolower(textoEntrada($_POST, 'email'));
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarCsrf();
    $senha = is_string($_POST['senha'] ?? null) ? $_POST['senha'] : '';
    $confirmacao = is_string($_POST['senha_confirmacao'] ?? null) ? $_POST['senha_confirmacao'] : '';
    if (mb_strlen($nome) < 3 || mb_strlen($nome) > 120) $mensagensErro[] = 'Informe um nome entre 3 e 120 caracteres.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 160) $mensagensErro[] = 'Informe um e-mail válido com até 160 caracteres.';
    if (mb_strlen($senha) < 8 || strlen($senha) > 72) $mensagensErro[] = 'Use uma senha com pelo menos 8 caracteres e até 72 bytes.';
    if (str_contains($senha, "\0")) $mensagensErro[] = 'A senha contém um caractere inválido.';
    if ($senha !== $confirmacao) $mensagensErro[] = 'As senhas informadas não são iguais.';
    if (!$mensagensErro) {
        require_once __DIR__ . '/../../../config/database.php';
        require_once __DIR__ . '/../../models/Usuarios.php';
        try {
            $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE LOWER(email) = :email');
            $stmt->execute(['email' => $email]);
            if ($stmt->fetch()) {
                $mensagensErro[] = 'Este endereço de e-mail já possui cadastro.';
            } else {
                (new Usuario($pdo))->cadastrar(['nome' => $nome, 'email' => $email, 'senha' => $senha]);
                header('Location: login.php?sucesso=cadastrado');
                exit;
            }
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            $mensagensErro[] = $exception->getCode() === '23505' ? 'Este endereço de e-mail já possui cadastro.' : 'Não foi possível criar a conta. Tente novamente.';
        }
    }
}
?>
<!doctype html><html lang="pt-BR">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Criar conta | Portal SI</title><link rel="stylesheet" href="../../../public/assets/css/auth.css"></head>
<body><main class="auth-layout">
<section class="auth-intro"><a class="brand" href="<?= e(portalUrl('public/index.php')) ?>"><span>SI</span> Portal Acadêmico</a><div><p class="eyebrow">FAÇA PARTE DA COMUNIDADE</p><h1>Sua próxima ideia começa aqui.</h1><p>Compartilhe projetos e participe das iniciativas de Sistemas de Informação.</p></div><p class="intro-footer">Faculdades ESUCRI · Portal SI</p></section>
<section class="auth-form"><div class="auth-card"><p class="eyebrow">VAMOS COMEÇAR</p><h2>Crie sua conta</h2><p class="muted">Preencha seus dados para participar do portal.</p>
<?php if ($mensagensErro): ?><div class="alert error" role="alert"><ul><?php foreach ($mensagensErro as $mensagem): ?><li><?= e($mensagem) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" action="cadastro_usuario.php"><?= campoCsrf() ?>
<label for="nome">Nome completo</label><input id="nome" name="nome" required minlength="3" maxlength="120" autocomplete="name" value="<?= e($nome) ?>" placeholder="Como você se chama?">
<label for="email">E-mail</label><input type="email" id="email" name="email" required maxlength="160" autocomplete="email" value="<?= e($email) ?>" placeholder="voce@instituicao.com">
<label for="senha">Senha</label><input type="password" id="senha" name="senha" required minlength="8" maxlength="72" autocomplete="new-password" placeholder="Pelo menos 8 caracteres">
<label for="senha_confirmacao">Confirmar senha</label><input type="password" id="senha_confirmacao" name="senha_confirmacao" required minlength="8" maxlength="72" autocomplete="new-password" placeholder="Repita sua senha">
<button type="submit">Criar minha conta <span aria-hidden="true">→</span></button></form>
<p class="form-footer">Já tem uma conta? <a href="login.php">Entrar</a></p><a class="back-link" href="<?= e(portalUrl('public/index.php')) ?>">← Voltar ao portal</a></div></section></main></body></html>
