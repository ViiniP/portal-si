<?php

require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/seguranca.php';

$caminhoPublico = '../../../public';

// A permissão é verificada antes de consultar o banco.
exigirPerfil('admin', 'login.php', $caminhoPublico . '/index.php');

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../models/Usuarios.php';

$usuarios = [];
$erro = '';

try {
    $usuarioModel = new Usuario($pdo);
    $usuarios = $usuarioModel->listar();
} catch (PDOException $exception) {
    http_response_code(500);
    error_log($exception->getMessage());
    $erro = 'Não foi possível carregar os usuários. Tente novamente.';
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Usuários | Portal SI</title>

    <link
        rel="stylesheet"
        href="<?= e($caminhoPublico) ?>/assets/css/styles.css?v=<?= filemtime(__DIR__ . '/../../../public/assets/css/styles.css') ?>"
    >

    <link
        rel="stylesheet"
        href="<?= e($caminhoPublico) ?>/assets/css/dashboard.css?v=<?= filemtime(__DIR__ . '/../../../public/assets/css/dashboard.css') ?>"
    >

    <link
        rel="stylesheet"
        href="<?= e($caminhoPublico) ?>/assets/css/usuarios.css?v=<?= filemtime(__DIR__ . '/../../../public/assets/css/usuarios.css') ?>"
    >
</head>

<body class="pagina-dashboard">
    <a class="pular-conteudo" href="#principal">
        Pular para o conteúdo
    </a>

    <header class="painel-topo">
        <div class="painel-container topo-interno">
            <div class="marca">
                <a href="dashboard.php">
                    <span class="marca-sigla">SI</span>
                    <span class="marca-nome">Portal Acadêmico</span>
                </a>
            </div>

            <nav
                class="painel-navegacao"
                aria-label="Navegação administrativa"
            >
                <a href="dashboard.php">Dashboard</a>

                <a
                    href="usuarios.php"
                    class="nav-atual"
                    aria-current="page"
                >
                    Usuários
                </a>
            </nav>

            <a href="logout.php" class="painel-sair">
                Sair
            </a>
        </div>
    </header>

    <main id="principal" class="painel-container usuarios-conteudo">
        <div class="secao-titulo">
            <p class="sobretitulo">GESTÃO DO PORTAL</p>
            <h1>Usuários</h1>
            <p>Consulte as contas cadastradas no Portal SI.</p>
        </div>

        <?php if ($erro !== ''): ?>
            <p class="usuarios-alerta" role="alert">
                <?= e($erro) ?>
            </p>

        <?php elseif (empty($usuarios)): ?>
            <p class="usuarios-vazio">
                Nenhum usuário cadastrado.
            </p>

        <?php else: ?>
            <p class="usuarios-total">
                Total de usuários: <strong><?= count($usuarios) ?></strong>
            </p>

            <div
                class="usuarios-tabela-container"
                role="region"
                aria-label="Lista de usuários cadastrados"
                tabindex="0"
            >
                <table class="usuarios-tabela">
                    <caption>Contas cadastradas no Portal SI</caption>

                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Nome</th>
                            <th scope="col">E-mail</th>
                            <th scope="col">Perfil</th>
                            <th scope="col">Status</th>
                            <th scope="col">Cadastro</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?= e((string) $usuario['id']) ?></td>
                                <td><?= e($usuario['nome']) ?></td>
                                <td><?= e($usuario['email']) ?></td>
                                <td><?= e($usuario['perfil']) ?></td>
                                <td><?= e($usuario['status']) ?></td>
                                <td>
                                    <?= e(
                                        (new DateTimeImmutable(
                                            $usuario['criado_em']
                                        ))->format('d/m/Y')
                                    ) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <a class="usuarios-voltar" href="dashboard.php">
            ← Voltar ao dashboard
        </a>
    </main>
</body>
</html>
