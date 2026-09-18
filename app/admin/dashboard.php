<?php

session_start();

// Protege o dashboard contra acesso sem login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Recupera os dados armazenados na sessão
$nome = $_SESSION['usuario_nome'];
$email = $_SESSION['usuario_email'];
$perfil = $_SESSION['usuario_perfil'];

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard | Portal SI</title>

    <link rel="stylesheet" href="../../public/css/dashboard.css">
</head>

<body>

    <header class="topo">

        <div class="topo-conteudo">
            <h2>Portal SI</h2>

            <div class="usuario-topo">
                <span>
                    <?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?>
                </span>

                <a href="logout.php" class="botao-sair">
                    Sair
                </a>
            </div>
        </div>

    </header>


    <main class="dashboard">

        <section class="boas-vindas">

            <p class="subtitulo">PAINEL ADMINISTRATIVO</p>

            <h1>
                Olá,
                <?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?>!
            </h1>

            <p>
                Bem-vindo ao Portal SI. Você está autenticado no sistema.
            </p>

        </section>


        <section class="cards">

            <div class="card">

                <span class="card-label">
                    Nome
                </span>

                <strong>
                    <?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?>
                </strong>

            </div>


            <div class="card">

                <span class="card-label">
                    E-mail
                </span>

                <strong>
                    <?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>
                </strong>

            </div>


            <div class="card">

                <span class="card-label">
                    Perfil
                </span>

                <strong>
                    <?= htmlspecialchars($perfil, ENT_QUOTES, 'UTF-8') ?>
                </strong>

            </div>

        </section>


        <section class="conteudo">

            <h2>Área do usuário</h2>

            <p>
                Este espaço poderá receber as funcionalidades
                disponíveis para o seu perfil.
            </p>

        </section>

    </main>

</body>

</html>