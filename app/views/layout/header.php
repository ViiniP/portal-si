<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= isset($titulo_pagina) ? $titulo_pagina . " | Portal SI" : "Portal de Comunicação SI - ESUCRI" ?>
    </title>

    <link rel="stylesheet" href="assets/css/styles.css?v=<?= filemtime(__DIR__ . '/../../../public/assets/css/styles.css') ?>">
</head>

<body>

    <!-- Cabeçalho Institucional do Portal -->
    <header class="cabecalho-principal">
        <div class="container container-flex">

            <div class="marca">
                <a href="index.php">
                    <span class="marca-sigla">SI</span>
                    <span class="marca-nome">Portal Acadêmico</span>
                </a>
            </div>

            <nav class="navegacao-global" aria-label="Navegação Principal">
                <ul class="menu-lista">
                    <li>
                        <a href="index.php">Início</a>
                    </li>

                    <li>
                        <a href="categoria.php?slug=acoes-sociais">
                            Ações Sociais
                        </a>
                    </li>

                    <li>
                        <a href="categoria.php?slug=extensao">
                            Extensão
                        </a>
                    </li>

                    <li>
                        <a href="../app/views/admin/login.php" class="btn-acesso">
                            Login
                        </a>
                    </li>
                </ul>
            </nav>

        </div>
    </header>

    <!-- Núcleo Informacional Exclusivo -->
    <main class="conteudo-principal">
