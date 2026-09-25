<?php

require_once __DIR__ . '/../../config/database.php';

$mensagensErro = [];

$nomeUsuario = '';
$emailUsuario = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recupera e limpa os dados enviados pelo formulário
    $nomeUsuario = trim($_POST['nome'] ?? '');
    $emailUsuario = trim($_POST['email'] ?? '');
    $senhaUsuario = $_POST['senha'] ?? '';
    $confirmarSenha = $_POST['senha_confirmacao'] ?? '';

    // Validação do nome
    if (mb_strlen($nomeUsuario) < 3) {
        $mensagensErro[] = 'Digite um nome com pelo menos 3 caracteres.';
    }

    // Validação do e-mail
    if (!filter_var($emailUsuario, FILTER_VALIDATE_EMAIL)) {
        $mensagensErro[] = 'Digite um endereço de e-mail válido.';
    }

    // Validação da senha
    if (mb_strlen($senhaUsuario) < 8) {
        $mensagensErro[] = 'Sua senha precisa possuir pelo menos 8 caracteres.';
    }

    // Verifica se as duas senhas são iguais
    if ($senhaUsuario !== $confirmarSenha) {
        $mensagensErro[] = 'As senhas informadas não são iguais.';
    }

    // Só realiza o cadastro se nenhuma validação apresentar erro
    if (empty($mensagensErro)) {

        try {

            $senhaCriptografada = password_hash(
                $senhaUsuario,
                PASSWORD_DEFAULT
            );

            $query = "
                INSERT INTO usuarios
                    (nome, email, senha_hash, perfil, status)
                VALUES
                    (:nome, :email, :senha, :perfil, :status)
            ";

            $comando = $pdo->prepare($query);

            $comando->bindValue(':nome', $nomeUsuario, PDO::PARAM_STR);
            $comando->bindValue(':email', $emailUsuario, PDO::PARAM_STR);
            $comando->bindValue(':senha', $senhaCriptografada, PDO::PARAM_STR);
            $comando->bindValue(':perfil', 'aluno', PDO::PARAM_STR);
            $comando->bindValue(':status', 'ativo', PDO::PARAM_STR);

            $comando->execute();

            header('Location: login.php?sucesso=cadastrado');
            exit;

        } catch (PDOException $erro) {

            // PostgreSQL: violação de campo UNIQUE
            if ($erro->getCode() === '23505') {
                $mensagensErro[] = 'Este endereço de e-mail já possui cadastro.';
            } else {
                $mensagensErro[] = 'Não foi possível realizar o cadastro.';
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Criar conta | Portal SI</title>

    <link rel="stylesheet" href="../../public/assets/css/cadastro.css">
</head>

<body>

    <main class="pagina-cadastro">

        <section class="card-cadastro">

            <div class="cabecalho">
                <h1>Criar uma conta</h1>
                <p>Preencha seus dados para acessar o Portal SI.</p>
            </div>

            <?php if (!empty($mensagensErro)): ?>

                <div class="alerta-erro">
                    <strong>Não foi possível realizar o cadastro:</strong>

                    <ul>
                        <?php foreach ($mensagensErro as $mensagem): ?>
                            <li>
                                <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

            <?php endif; ?>

            <form method="POST" action="cadastro.php">

                <div class="campo">
                    <label for="nome">Nome completo</label>

                    <input
                        type="text"
                        name="nome"
                        id="nome"
                        placeholder="Digite seu nome"
                        required
                    >
                </div>

                <div class="campo">
                    <label for="email">E-mail institucional</label>

                    <input
                        type="email"
                        name="email"
                        id="email"
                        placeholder="seuemail@instituicao.com"
                        required
                    >
                </div>

                <div class="campo">
                    <label for="senha">Senha</label>

                    <input
                        type="password"
                        name="senha"
                        id="senha"
                        placeholder="Mínimo de 8 caracteres"
                        required
                    >
                </div>

                <div class="campo">
                    <label for="senha_confirmacao">Confirmar senha</label>

                    <input
                        type="password"
                        name="senha_confirmacao"
                        id="senha_confirmacao"
                        placeholder="Digite novamente sua senha"
                        required
                    >
                </div>

                <button class="botao-cadastrar" type="submit">
                    Criar conta
                </button>

            </form>

            <div class="rodape-card">
                <p>
                    Já possui uma conta?
                    <a href="login.php">Fazer login</a>
                </p>
            </div>

        </section>

    </main>

</body>

</html>