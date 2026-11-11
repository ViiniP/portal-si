<?php
require_once __DIR__ . '/includes/verifica_sessao.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../helpers/conteudos.php';
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
$stmt = $pdo->prepare('SELECT * FROM conteudos WHERE id=:id');
$stmt->execute(['id' => $id ?: 0]);
$conteudo = $stmt->fetch();
if (!$conteudo || !podeGerenciarConteudo($conteudo) || (!podePublicar() && !in_array($conteudo['status'], ['rascunho','revisao'], true))) {
    header('Location: conteudo_listar.php?erro=Conteúdo+não+encontrado+ou+sem+permissão.');
    exit;
}
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarCsrf();
    try {
        $stmt = $pdo->prepare('DELETE FROM conteudos WHERE id=:id');
        $stmt->execute(['id' => $id]);
        removerImagem($conteudo['imagem_capa']);
        header('Location: conteudo_listar.php?sucesso=excluido');
        exit;
    } catch (PDOException $exception) {
        error_log($exception->getMessage());
        $erro = 'Não foi possível excluir a publicação. Tente novamente.';
    }
}
$titulo_pagina = 'Excluir publicação';
require __DIR__ . '/includes/header.php';
?>
<div class="page-heading"><div><p class="eyebrow">GESTÃO DE CONTEÚDO</p><h1>Excluir publicação</h1><p>Confira o conteúdo antes de confirmar.</p></div></div>
<section class="form-container delete-panel"><span class="delete-icon" aria-hidden="true">!</span><h2>Deseja excluir este conteúdo?</h2><p class="delete-title"><?= e($conteudo['titulo']) ?></p><p>A publicação e sua imagem serão removidas permanentemente. Esta ação não pode ser desfeita.</p>
<?php if ($erro): ?><div class="alert alert-danger" role="alert"><?= e($erro) ?></div><?php endif; ?>
<form method="post"><?= campoCsrf() ?><div class="editor-actions"><a href="conteudo_listar.php" class="btn btn-secondary">Cancelar</a><button class="btn btn-danger" type="submit">Sim, excluir publicação</button></div></form></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
