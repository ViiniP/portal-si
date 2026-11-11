<?php
require_once __DIR__ . '/verifica_sessao.php';
$paginaAtual = basename($_SERVER['SCRIPT_NAME']);
?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= e($titulo_pagina ?? 'Painel') ?> | Portal SI</title><link rel="stylesheet" href="css/admin.css"></head>
<body><a href="#principal" class="skip-link">Pular para o conteúdo</a>
<aside class="sidebar">
<a class="brand" href="index.php"><span class="brand-icon">SI</span><span>Portal Acadêmico<small>ESUCRI · SISTEMAS DE INFORMAÇÃO</small></span></a>
<p class="nav-label">ESPAÇO DE TRABALHO</p>
<nav aria-label="Navegação do painel">
<a href="index.php" <?= $paginaAtual === 'index.php' ? 'aria-current="page"' : '' ?>><span aria-hidden="true">◫</span> Visão geral</a>
<a href="conteudo_listar.php" <?= $paginaAtual === 'conteudo_listar.php' ? 'aria-current="page"' : '' ?>><span aria-hidden="true">▤</span> Publicações</a>
<a href="conteudo_cadastrar.php" <?= $paginaAtual === 'conteudo_cadastrar.php' ? 'aria-current="page"' : '' ?>><span aria-hidden="true">＋</span> Novo conteúdo</a>
<a href="<?= e(portalUrl('public/index.php')) ?>"><span aria-hidden="true">↗</span> Visitar o portal</a>
</nav>
<div class="sidebar-note"><strong>Ideias que fazem a diferença.</strong><p>Compartilhe o que acontece na nossa comunidade acadêmica.</p></div>
<div class="user-info"><span class="avatar"><?= e(mb_strtoupper(mb_substr($_SESSION['usuario_nome'], 0, 1))) ?></span><div><strong><?= e($_SESSION['usuario_nome']) ?></strong><small><?= e(ucfirst($_SESSION['usuario_perfil'])) ?></small></div></div>
<form action="logout.php" method="post" class="logout-form"><?= campoCsrf() ?><button type="submit">Sair da conta <span aria-hidden="true">↗</span></button></form>
</aside>
<div class="workspace"><header class="workspace-topbar"><span>Comunidade / <strong><?= e($titulo_pagina ?? 'Painel') ?></strong></span><span class="institution">Faculdades ESUCRI</span></header><main id="principal" class="main-content">
