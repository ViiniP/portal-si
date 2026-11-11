<?php
require_once __DIR__ . '/includes/verifica_sessao.php';
require_once __DIR__ . '/../../../config/database.php';
$filtro = podePublicar() ? '' : ' WHERE autor_id = :autor';
$params = podePublicar() ? [] : ['autor' => $_SESSION['usuario_id']];
$stmt = $pdo->prepare("SELECT COUNT(*) AS total, COUNT(*) FILTER (WHERE status='publicado') AS publicados, COUNT(*) FILTER (WHERE status IN ('rascunho','revisao')) AS preparacao FROM conteudos" . $filtro);
$stmt->execute($params);
$totais = $stmt->fetch();
$stmt = $pdo->prepare('SELECT id, titulo, status, atualizado_em FROM conteudos' . $filtro . ' ORDER BY atualizado_em DESC LIMIT 5');
$stmt->execute($params);
$recentes = $stmt->fetchAll();
$titulo_pagina = 'Visão geral';
require __DIR__ . '/includes/header.php';
?>
<div class="page-heading"><div><p class="eyebrow">SEU ESPAÇO NO PORTAL SI</p><h1>Olá, <?= e(explode(' ', $_SESSION['usuario_nome'])[0]) ?>.</h1><p>Vamos compartilhar algo que vale a pena conhecer?</p></div><a class="btn btn-primary" href="conteudo_cadastrar.php">＋ Nova publicação</a></div>
<section class="welcome-banner"><div><span class="eyebrow">CONHECIMENTO EM MOVIMENTO</span><h2>Da sala de aula<br>para a comunidade.</h2><p>Projetos de extensão, descobertas e histórias que merecem ser compartilhadas.</p><a href="<?= e(portalUrl('public/index.php')) ?>">Explorar o portal <span aria-hidden="true">↗</span></a></div><div class="banner-art" aria-hidden="true"><span>SI</span><small>CRIAR · CONECTAR · TRANSFORMAR</small></div></section>
<section class="cards-grid" aria-label="Resumo das publicações">
<div class="stat-card"><span>Total de publicações</span><strong><?= (int) $totais['total'] ?></strong><small><?= podePublicar() ? 'Conteúdos da comunidade' : 'Seus conteúdos' ?></small></div>
<div class="stat-card"><span>Publicadas</span><strong><?= (int) $totais['publicados'] ?></strong><small>Disponíveis para os leitores</small></div>
<div class="stat-card"><span>Em preparação</span><strong><?= (int) $totais['preparacao'] ?></strong><small>Rascunhos e conteúdos em revisão</small></div>
</section>
<section class="panel"><div class="panel-heading"><div><h2>Atividade recente</h2><p>Continue de onde parou.</p></div><a href="conteudo_listar.php">Ver publicações →</a></div>
<?php if (!$recentes): ?><div class="empty-state"><h3>Sua primeira história começa aqui.</h3><p>Crie um conteúdo e compartilhe suas ideias com a comunidade.</p><a class="btn btn-primary" href="conteudo_cadastrar.php">Criar publicação</a></div><?php else: ?>
<div class="recent-list"><?php foreach ($recentes as $item): ?><div class="recent-item"><div><strong><?= e($item['titulo']) ?></strong><small>Atualizado em <?= date('d/m/Y', strtotime($item['atualizado_em'])) ?></small></div><span class="badge <?= e($item['status']) ?>"><?= e(['rascunho'=>'Rascunho','revisao'=>'Em revisão','publicado'=>'Publicado','arquivado'=>'Arquivado'][$item['status']]) ?></span></div><?php endforeach; ?></div><?php endif; ?></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
