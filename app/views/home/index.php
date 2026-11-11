<?php
require_once __DIR__ . '/../../helpers/portal.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../models/Conteudo.php';
$categoriaSlug = textoEntrada($_GET, 'categoria');
$categoriaAtual = null;
if ($categoriaSlug !== '') {
    $stmt = $pdo->prepare('SELECT nome, descricao FROM categorias WHERE slug=:slug AND ativa=TRUE');
    $stmt->execute(['slug' => $categoriaSlug]);
    $categoriaAtual = $stmt->fetch();
    if (!$categoriaAtual) http_response_code(404);
}
$titulo_pagina = $categoriaSlug !== '' ? ($categoriaAtual['nome'] ?? 'Categoria não encontrada') : 'Página inicial';
$conteudos_recentes = (new Conteudo($pdo))->listarPublicados(60, $categoriaSlug ?: null);

require_once __DIR__ . '/../layout/header.php';
?>

<!-- Seção Hero / Destaque de Abertura -->
<section class="secao-hero" aria-labelledby="titulo-hero">
    <div class="container">
        <p class="hero-eyebrow">SISTEMAS DE INFORMAÇÃO · ESUCRI</p>
        <h1 id="titulo-hero"><?= e($categoriaSlug !== '' ? $titulo_pagina : 'Conhecimento que conecta e transforma.') ?></h1>

        <p>
            <?= e($categoriaSlug !== '' ? ($categoriaAtual['descricao'] ?? 'Explore outras categorias no menu do portal.') : 'Projetos, pesquisas e experiências que aproximam nossa comunidade acadêmica do mundo. Explore o que estamos construindo juntos.') ?>
        </p>
        <a class="hero-cta" href="#titulo-publicacoes">Explorar publicações ↓</a>
    </div>
</section>

<!-- Seção com Fluxo de Publicações -->
<section class="secao-publicacoes container" aria-labelledby="titulo-publicacoes">

    <h2 id="titulo-publicacoes" class="subtitulo-secao">
        <?= $categoriaSlug ? 'Publicações da categoria' : 'Últimas publicações' ?>
    </h2>

    <?php if (empty($conteudos_recentes)): ?>

        <p class="alerta-vazio">
            Nenhuma publicação foi disponibilizada até o momento.
        </p>

    <?php else: ?>

        <div class="grade-publicacoes">

            <?php foreach ($conteudos_recentes as $item): ?>

                <article class="cartao-conteudo">
                    <?php if ($capa = imagemConteudo($item['imagem_capa'])): ?>
                        <img class="cartao-imagem" src="<?= e($capa) ?>" alt="" loading="lazy">
                    <?php endif; ?>

                    <header class="cartao-cabecalho">
                        <span class="cartao-categoria">
                            <?= e($item['categoria_nome']) ?>
                        </span>

                        <h3 class="cartao-titulo">
                            <?= e($item['titulo']) ?>
                        </h3>
                    </header>

                    <p class="cartao-resumo">
                        <?= e($item['resumo']) ?>
                    </p>

                    <footer class="cartao-rodape">
                        <time datetime="<?= e($item['publicado_em']) ?>">
                            <?= date('d/m/Y', strtotime($item['publicado_em'])) ?>
                        </time>

                        <a href="<?= e(portalUrl('public/conteudo.php?slug=' . rawurlencode($item['slug']))) ?>" class="cartao-link" aria-label="<?= e('Ler: ' . $item['titulo']) ?>">
                            Ler publicação
                        </a>
                    </footer>

                </article>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</section>

<?php
require_once __DIR__ . '/../layout/footer.php';
?>
