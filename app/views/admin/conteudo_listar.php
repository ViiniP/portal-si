<?php
require_once __DIR__ . '/includes/verifica_sessao.php';
require_once __DIR__ . '/../../../config/database.php';
$busca = textoEntrada($_GET, 'busca');
$status = textoEntrada($_GET, 'status');
$rotulos = ['rascunho'=>'Rascunho','revisao'=>'Em revisão','publicado'=>'Publicado','arquivado'=>'Arquivado'];
$where = [];
$params = [];
if (!podePublicar()) { $where[] = 'c.autor_id=:autor'; $params['autor'] = $_SESSION['usuario_id']; }
if ($busca !== '') { $where[] = 'c.titulo ILIKE :busca'; $params['busca'] = '%' . $busca . '%'; }
if (isset($rotulos[$status])) { $where[] = 'c.status=:status'; $params['status'] = $status; }
$stmt = $pdo->prepare('SELECT c.*, cat.nome AS categoria_nome, u.nome AS autor_nome FROM conteudos c JOIN categorias cat ON c.categoria_id=cat.id JOIN usuarios u ON c.autor_id=u.id' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY c.atualizado_em DESC, c.id DESC');
$stmt->execute($params);
$conteudos = $stmt->fetchAll();
$titulo_pagina = 'Publicações';
require __DIR__ . '/includes/header.php';
?>
<div class="page-heading"><div><p class="eyebrow">GESTÃO DE CONTEÚDO</p><h1>Suas ideias ganham espaço.</h1><p><?= podePublicar() ? 'Organize e acompanhe as publicações do portal.' : 'Acompanhe suas publicações e envie novidades para revisão.' ?></p></div><a class="btn btn-primary" href="conteudo_cadastrar.php">＋ Nova publicação</a></div>
<?php if (textoEntrada($_GET, 'sucesso')): ?><div class="alert alert-success" role="status">Pronto! Operação realizada com sucesso.</div><?php endif; ?>
<?php if ($erro = textoEntrada($_GET, 'erro')): ?><div class="alert alert-danger" role="alert"><?= e($erro) ?></div><?php endif; ?>
<section class="panel"><form class="filter-bar" method="get"><div><label for="busca">Buscar publicação</label><input class="form-control" type="search" id="busca" name="busca" value="<?= e($busca) ?>" placeholder="Pesquisar pelo título…"></div><div><label for="status">Situação</label><select class="form-control" id="status" name="status"><option value="">Todas as situações</option><?php foreach ($rotulos as $valor=>$rotulo): ?><option value="<?= $valor ?>" <?= $status === $valor ? 'selected' : '' ?>><?= $rotulo ?></option><?php endforeach; ?></select></div><button class="btn btn-primary" type="submit">Filtrar</button><?php if ($busca || $status): ?><a class="btn btn-secondary" href="conteudo_listar.php">Limpar</a><?php endif; ?></form>
<div class="list-caption"><strong><?= count($conteudos) ?> publicação(ões)</strong><span><?= podePublicar() ? 'Conteúdos da comunidade' : 'Seu acervo' ?></span></div>
<?php if (!$conteudos): ?><div class="empty-state"><span class="empty-symbol" aria-hidden="true">▤</span><h2>Nenhuma publicação por aqui.</h2><p><?= $busca || $status ? 'Tente outro título ou altere os filtros.' : 'Comece compartilhando um projeto, uma notícia ou uma descoberta.' ?></p><a class="btn btn-secondary" href="<?= $busca || $status ? 'conteudo_listar.php' : 'conteudo_cadastrar.php' ?>"><?= $busca || $status ? 'Limpar filtros' : 'Criar publicação' ?></a></div>
<?php else: ?><div class="table-container" role="region" aria-label="Publicações" tabindex="0"><table><thead><tr><th scope="col">Publicação</th><th scope="col">Categoria</th><th scope="col">Situação</th><th scope="col">Atualização</th><th scope="col">Ações</th></tr></thead><tbody>
<?php foreach ($conteudos as $item): ?><tr><td><div class="publication-cell"><?php if ($capa=imagemConteudo($item['imagem_capa'])): ?><img src="<?= e($capa) ?>" alt=""><?php else: ?><span class="cover-placeholder" aria-hidden="true">SI</span><?php endif; ?><div><strong><?= e($item['titulo']) ?></strong><small><?= e($item['autor_nome']) ?></small></div></div></td><td><?= e($item['categoria_nome']) ?></td><td><span class="badge <?= e($item['status']) ?>"><?= e($rotulos[$item['status']]) ?></span></td><td class="date-cell"><?= date('d/m/Y', strtotime($item['atualizado_em'])) ?></td><td><div class="table-actions">
<?php if (podePublicar() || in_array($item['status'], ['rascunho','revisao'], true)): ?><a class="btn btn-secondary btn-sm" href="conteudos_editar.php?id=<?= (int) $item['id'] ?>">Editar<span class="sr-only"> <?= e($item['titulo']) ?></span></a><a class="btn btn-danger-outline btn-sm" href="conteudos_excluir.php?id=<?= (int) $item['id'] ?>">Excluir<span class="sr-only"> <?= e($item['titulo']) ?></span></a><?php endif; ?>
<?php if ($item['status'] === 'publicado'): ?><a class="btn btn-secondary btn-sm" href="<?= e(portalUrl('public/conteudo.php?slug=' . rawurlencode($item['slug']))) ?>">Ver<span class="sr-only"> <?= e($item['titulo']) ?></span></a><?php endif; ?>
</div></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
