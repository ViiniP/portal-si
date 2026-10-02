<?php

$titulo_pagina = "Página Inicial";

require_once __DIR__ . '/../layout/header.php';
?>

<!-- Seção Hero / Destaque de Abertura -->
<section class="secao-hero" aria-labelledby="titulo-hero">
    <div class="container">
        <h1 id="titulo-hero">
            Portal de Sistemas de Informação — ESUCRI
        </h1>

        <p>
            A vitrine acadêmica dedicada à promoção, integração e difusão
            das práticas extensionistas e científicas de nossa comunidade acadêmica.
        </p>
    </div>
</section>

<!-- Seção com Fluxo de Publicações -->
<section class="secao-publicacoes container" aria-labelledby="titulo-publicacoes">

    <h2 id="titulo-publicacoes" class="subtitulo-secao">
        Últimas Atualizações
    </h2>

    <?php if (empty($conteudos_recentes)): ?>

        <p class="alerta-vazio">
            Nenhuma publicação foi disponibilizada até o momento.
        </p>

    <?php else: ?>

        <div class="grade-publicacoes">

            <?php foreach ($conteudos_recentes as $item): ?>

                <article class="cartao-conteudo">

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

                        <a href="conteudo.php?slug=<?= e($item['slug']) ?>" class="cartao-link">
                            Acessar
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