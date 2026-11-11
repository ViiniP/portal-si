<?php
require_once __DIR__ . '/../../helpers/portal.php';
?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="description" content="Projetos, pesquisas e notícias da comunidade de Sistemas de Informação das Faculdades ESUCRI."><title><?= e($titulo_pagina ?? 'Portal Acadêmico') ?> | Portal SI</title><link rel="stylesheet" href="<?= e(portalUrl('public/assets/css/styles.css')) ?>"></head>
<body><a class="skip-link" href="#conteudo-principal">Pular para o conteúdo</a>
<header class="cabecalho-principal"><div class="container container-flex"><div class="marca"><a href="<?= e(portalUrl('public/index.php')) ?>"><span class="marca-sigla">SI</span><span class="marca-nome">Portal Acadêmico</span></a></div>
<nav class="navegacao-global" aria-label="Navegação principal"><ul class="menu-lista">
<li><a href="<?= e(portalUrl('public/index.php')) ?>">Início</a></li>
<li><a href="<?= e(portalUrl('public/categoria.php?slug=acoes-sociais')) ?>">Ações sociais</a></li>
<li><a href="<?= e(portalUrl('public/categoria.php?slug=extensao')) ?>">Extensão</a></li>
<li><a href="<?= e(portalUrl('public/categoria.php?slug=eventos-academicos')) ?>">Eventos</a></li>
<li><a href="<?= e(portalUrl('app/views/admin/login.php')) ?>" class="btn-acesso">Acessar portal ↗</a></li>
</ul></nav></div></header><main id="conteudo-principal" class="conteudo-principal">
