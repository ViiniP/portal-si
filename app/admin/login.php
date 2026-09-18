<?php

// Inicia ou recupera a sessão
session_start();

require_once __DIR__ . '/../../config/database.php';

$erro = '';

// Se o usuário já estiver autenticado,
// não precisa fazer login novamente
if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
}


// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recupera os dados enviados pelo formulário
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';


    // Verifica se os campos foram preenchidos
    if (empty($email) || empty($senha)) {

        $erro = 'Preencha todos os campos para prosseguir.';

    } else {

        // Busca pelo e-mail apenas entre usuários ativos
        $sql = "
            SELECT
                id,
                nome,
                email,
                senha_hash,
                perfil
            FROM usuarios
            WHERE email = :email
              AND status = 'ativo'
            LIMIT 1
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(
            ':email',
            $email,
            PDO::PARAM_STR
        );

        $stmt->execute();

        // Recupera o usuário encontrado
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);


        // Verifica:
        // 1. Se o usuário existe
        // 2. Se a senha corresponde ao hash salvo
        if (
            $usuario &&
            password_verify($senha, $usuario['senha_hash'])
        ) {

            // Gera um novo ID para a sessão
            session_regenerate_id(true);

            // Guarda os dados do usuário na sessão
            $_SESSION['usuario_id'] = (int) $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['usuario_perfil'] = $usuario['perfil'];
            $_SESSION['ultimo_acesso'] = time();

            // Envia o usuário para o painel
            header('Location: dashboard.php');
            exit;

        } else {

            // Mensagem genérica por segurança
            $erro = 'E-mail ou senha inválidos.';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | Portal SI</title>
    <link rel="stylesheet" href="../../public/css/login.css">
</head>

<body>

   <main>

    <section class="login-container">

        <h1>Acesso ao Portal</h1>

        <p class="login-subtitulo">
            Entre com seus dados para acessar o Portal SI.
        </p>

        <?php if (!empty($erro)): ?>

            <div class="alerta-erro">
                <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
            </div>

        <?php endif; ?>


        <?php if (
            isset($_GET['sucesso']) &&
            $_GET['sucesso'] === 'cadastrado'
        ): ?>

            <div class="alerta-sucesso">
                Cadastro realizado com sucesso! Faça seu login.
            </div>

        <?php endif; ?>


        <form action="login.php" method="POST">

            <div class="campo">

                <label for="email">
                    E-mail
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="seuemail@instituicao.com"
                    required
                >

            </div>


            <div class="campo">

                <label for="senha">
                    Senha
                </label>

                <input
                    type="password"
                    id="senha"
                    name="senha"
                    placeholder="Digite sua senha"
                    required
                >

            </div>


            <button
                type="submit"
                class="botao-entrar"
            >
                Entrar
            </button>

        </form>


        <div class="login-rodape">

            <p>
                Ainda não possui uma conta?
                <a href="cadastro.php">Criar conta</a>
            </p>

        </div>

    </section>

</main>

</body>

</html>