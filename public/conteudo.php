<?php
require_once __DIR__ . '/../app/helpers/portal.php';
require_once __DIR__ . '/../config/database.php';
$stmt = $pdo->prepare("SELECT c.*, cat.nome AS categoria_nome, cat.slug AS categoria_slug, u.nome AS autor_nome FROM conteudos c JOIN categorias cat ON cat.id=c.categoria_id JOIN usuarios u ON u.id=c.autor_id WHERE c.slug=:slug AND c.status='publicado' AND c.publicado_em <= NOW() AND cat.ativa=TRUE");
$stmt->execute(['slug' => textoEntrada($_GET, 'slug')]);
$conteudo = $stmt->fetch();
if (!$conteudo) http_response_code(404);
$titulo_pagina = $conteudo['titulo'] ?? 'Publicação não encontrada';
require __DIR__ . '/../app/views/layout/header.php';
?>
<article class="article-page container"><a class="article-back" href="<?= e(portalUrl('public/index.php')) ?>">← Voltar às publicações</a>
<?php if (!$conteudo): ?><div class="article-body"><h1>Publicação não encontrada.</h1><p>Este conteúdo não está disponível. Explore as outras publicações do portal.</p></div>
<?php else: ?>
<header class="article-heading"><a class="cartao-categoria" href="<?= e(portalUrl('public/categoria.php?slug=' . rawurlencode($conteudo['categoria_slug']))) ?>"><?= e($conteudo['categoria_nome']) ?></a><h1><?= e($conteudo['titulo']) ?></h1><p class="article-summary"><?= e($conteudo['resumo']) ?></p><p class="article-meta">Por <?= e($conteudo['autor_nome']) ?> · <time datetime="<?= date('c', strtotime($conteudo['publicado_em'])) ?>"><?= date('d/m/Y', strtotime($conteudo['publicado_em'])) ?></time></p></header>
<?php if ($capa = imagemConteudo($conteudo['imagem_capa'])): ?><img class="article-cover" src="<?= e($capa) ?>" alt="<?= e($conteudo['titulo']) ?>"><?php endif; ?>
<div class="article-body"><?= nl2br(e($conteudo['corpo'])) ?></div>
<?php if (!empty($conteudo['link_youtube']) && youtubeValido($conteudo['link_youtube'])): ?><a class="video-link" href="<?= e($conteudo['link_youtube']) ?>" target="_blank" rel="noopener noreferrer">Assistir ao vídeo no YouTube ↗</a><?php endif; ?>
<?php endif; ?></article>
<?php require __DIR__ . '/../app/views/layout/footer.php'; ?>
