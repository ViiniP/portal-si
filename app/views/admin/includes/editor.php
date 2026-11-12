<?php
require_once __DIR__ . '/verifica_sessao.php';
require_once __DIR__ . '/../../../../config/database.php';
require_once __DIR__ . '/../../../helpers/conteudos.php';
$modoEdicao = $modoEdicao ?? false;
$conteudo = ['titulo' => '', 'categoria_id' => '', 'resumo' => '', 'corpo' => '', 'link_youtube' => '', 'status' => 'rascunho', 'imagem_capa' => null];
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if ($modoEdicao) {
    $stmt = $pdo->prepare('SELECT * FROM conteudos WHERE id = :id');
    $stmt->execute(['id' => $id ?: 0]);
    $conteudo = $stmt->fetch();
    if (!$conteudo || !podeGerenciarConteudo($conteudo)) {
        header('Location: conteudo_listar.php?erro=Conteúdo+não+encontrado+ou+sem+permissão.');
        exit;
    }
    // Editores podem editar apenas seus rascunhos e conteúdos em revisão.
    if (!podePublicar() && !in_array($conteudo['status'], ['rascunho', 'revisao'], true)) {
        header('Location: conteudo_listar.php?erro=Este+conteúdo+deve+ser+editado+por+um+administrador.');
        exit;
    }
}
$categorias = $pdo->query('SELECT id, nome FROM categorias WHERE ativa = TRUE ORDER BY ordem, nome')->fetchAll();
$erros = [];
$dados = $conteudo;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarCsrf();
    foreach (['titulo', 'categoria_id', 'resumo', 'corpo', 'link_youtube', 'status'] as $campo) $dados[$campo] = textoEntrada($_POST, $campo);
    $erros = validarConteudo($dados, $categorias);
    $novaImagem = null;
    if (!$erros) {
        try {
            if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] !== UPLOAD_ERR_NO_FILE) $novaImagem = salvarImagem($_FILES['imagem']);
            $params = [
                'titulo' => $dados['titulo'], 'categoria_id' => (int) $dados['categoria_id'],
                'resumo' => $dados['resumo'] ?: mb_substr($dados['corpo'], 0, 220),
                'corpo' => $dados['corpo'], 'link_youtube' => $dados['link_youtube'] ?: null,
                'status' => $dados['status'], 'imagem_capa' => $novaImagem ?? $conteudo['imagem_capa'],
                'publicado_em' => $dados['status'] === 'publicado' ? ($conteudo['publicado_em'] ?? date('Y-m-d H:i:s')) : null,
            ];
            if ($modoEdicao) {
                $params['id'] = $id;
                $stmt = $pdo->prepare('UPDATE conteudos SET titulo=:titulo, categoria_id=:categoria_id, resumo=:resumo, corpo=:corpo, link_youtube=:link_youtube, status=:status, imagem_capa=:imagem_capa, publicado_em=:publicado_em WHERE id=:id');
            } else {
                $params['slug'] = slugConteudo($dados['titulo']);
                $params['autor_id'] = $_SESSION['usuario_id'];
                $stmt = $pdo->prepare('INSERT INTO conteudos (titulo, categoria_id, resumo, corpo, link_youtube, status, imagem_capa, publicado_em, slug, autor_id) VALUES (:titulo, :categoria_id, :resumo, :corpo, :link_youtube, :status, :imagem_capa, :publicado_em, :slug, :autor_id)');
            }
            $stmt->execute($params);
            // A capa anterior é preservada até a gravação bem-sucedida no banco.
            if ($novaImagem) removerImagem($conteudo['imagem_capa']);
            header('Location: conteudo_listar.php?sucesso=salvo');
            exit;
        } catch (RuntimeException $exception) {
            if ($novaImagem) removerImagem($novaImagem);
            error_log($exception->getMessage());
            $erros[] = $exception instanceof PDOException ? 'Não foi possível salvar o conteúdo. Tente novamente.' : $exception->getMessage();
        }
    }
}
$titulo_pagina = $modoEdicao ? 'Editar conteúdo' : 'Novo conteúdo';
require __DIR__ . '/header.php';
?>
<div class="page-heading"><div><p class="eyebrow">COMPARTILHE CONHECIMENTO</p><h1><?= e($titulo_pagina) ?></h1><p>Uma nova história para conectar nossa comunidade.</p></div><a class="btn btn-secondary" href="conteudo_listar.php">← Voltar às publicações</a></div>
<?php if ($erros): ?><div class="alert alert-danger" role="alert"><strong>Confira os dados antes de continuar.</strong><ul><?php foreach ($erros as $erro): ?><li><?= e($erro) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" enctype="multipart/form-data" class="editor-grid">
<?= campoCsrf() ?>
<section class="form-container"><div class="section-heading"><span class="step">01</span><div><h2>Conteúdo da publicação</h2><p>Os campos com * são obrigatórios.</p></div></div>
<div class="form-group"><label for="titulo">Título *</label><input class="form-control" id="titulo" name="titulo" maxlength="200" required value="<?= e($dados['titulo']) ?>" placeholder="Dê um título à sua publicação"></div>
<div class="form-group"><label for="resumo">Resumo</label><textarea class="form-control resumo" id="resumo" name="resumo" maxlength="500" placeholder="Uma breve apresentação para os leitores"><?= e($dados['resumo']) ?></textarea><small>Até 500 caracteres. Se vazio, usaremos o início do texto.</small></div>
<div class="form-group"><label for="corpo">Texto completo *</label><textarea class="form-control corpo" id="corpo" name="corpo" required placeholder="Conte sua história, apresente seu projeto ou compartilhe uma novidade…"><?= e($dados['corpo']) ?></textarea><small>O texto será exibido com os parágrafos que você escrever.</small></div>
</section>
<aside class="editor-options"><section class="form-container"><div class="section-heading"><span class="step">02</span><div><h2>Organização</h2><p>Defina onde e como aparecer.</p></div></div>
<div class="form-group"><label for="categoria_id">Categoria *</label><select class="form-control" id="categoria_id" name="categoria_id" required><option value="">Selecione uma categoria</option><?php foreach ($categorias as $cat): ?><option value="<?= (int) $cat['id'] ?>" <?= (int) $dados['categoria_id'] === (int) $cat['id'] ? 'selected' : '' ?>><?= e($cat['nome']) ?></option><?php endforeach; ?></select></div>
<div class="form-group"><label for="status">Situação *</label><select class="form-control" id="status" name="status"><?php foreach (['rascunho' => 'Rascunho', 'revisao' => 'Em revisão', 'publicado' => 'Publicado', 'arquivado' => 'Arquivado'] as $valor => $rotulo): ?><?php if (podePublicar() || in_array($valor, ['rascunho', 'revisao'], true)): ?><option value="<?= $valor ?>" <?= $dados['status'] === $valor ? 'selected' : '' ?>><?= $rotulo ?></option><?php endif; ?><?php endforeach; ?></select><small><?= podePublicar() ? 'Selecione Publicado para exibir no portal.' : 'Envie para revisão para que um administrador publique.' ?></small></div></section>
<section class="form-container"><div class="section-heading"><span class="step">03</span><div><h2>Mídia</h2><p>Enriqueça sua publicação.</p></div></div>
<?php if ($capa = imagemConteudo($conteudo['imagem_capa'])): ?><img class="cover-preview" src="<?= e($capa) ?>" alt="Capa atual da publicação"><?php endif; ?>
<div class="form-group"><label for="imagem"><?= $modoEdicao ? 'Substituir capa' : 'Imagem de capa' ?></label><input class="form-control file-input" type="file" id="imagem" name="imagem" accept="image/jpeg,image/png,image/gif"><small>JPG, PNG ou GIF. Máximo de 2 MB.<?= $modoEdicao ? ' Deixe vazio para manter a capa.' : '' ?></small></div>
<div class="form-group"><label for="link_youtube">Vídeo do YouTube</label><input class="form-control" type="url" id="link_youtube" name="link_youtube" maxlength="255" value="<?= e($dados['link_youtube']) ?>" placeholder="https://www.youtube.com/watch?v=…"></div></section>
<div class="editor-actions"><button class="btn btn-primary" type="submit">Salvar publicação</button><a class="btn btn-secondary" href="conteudo_listar.php">Cancelar</a></div></aside></form>
<?php require __DIR__ . '/footer.php'; ?>
