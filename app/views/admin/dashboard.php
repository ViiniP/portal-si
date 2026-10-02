<?php

require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/seguranca.php';

// Permite compartilhar a tela com a entrada em app/admin/dashboard.php.
$caminhoPublico = $caminhoPublico ?? '../../../public';
exigirPerfil('admin', 'login.php', $caminhoPublico . '/index.php');

$nome = $_SESSION['usuario_nome'] ?? '';
$email = $_SESSION['usuario_email'] ?? '';
$modulos = [
    ['icone' => 'documento', 'nome' => 'Conteúdos', 'descricao' => 'Notícias, projetos e histórias que conectam nossa comunidade.', 'cor' => 'azul'],
    ['icone' => 'usuarios', 'nome' => 'Usuários', 'descricao' => 'Pessoas, perfis e permissões de acesso ao Portal SI.', 'cor' => 'violeta'],
    ['icone' => 'categoria', 'nome' => 'Categorias', 'descricao' => 'Organização das publicações por assuntos e áreas de interesse.', 'cor' => 'ciano'],
    ['icone' => 'calendario', 'nome' => 'Eventos', 'descricao' => 'Encontros e experiências que fazem parte da vida acadêmica.', 'cor' => 'laranja'],
];
$icone = static function (string $id): void {
    echo '<svg class="icone" aria-hidden="true"><use href="#icone-' . e($id) . '"></use></svg>';
};
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Portal SI</title>
    <link rel="stylesheet" href="<?= e($caminhoPublico) ?>/assets/css/styles.css?v=<?= filemtime(__DIR__ . '/../../../public/assets/css/styles.css') ?>">
    <link rel="stylesheet" href="<?= e($caminhoPublico) ?>/assets/css/dashboard.css?v=<?= filemtime(__DIR__ . '/../../../public/assets/css/dashboard.css') ?>">
</head>
<body class="pagina-dashboard">
    <a class="pular-conteudo" href="#principal">Pular para o conteúdo</a>
    <svg class="biblioteca-icones" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <symbol id="icone-painel" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></symbol>
        <symbol id="icone-seta" viewBox="0 0 24 24"><path d="M5 12h14m-6-6 6 6-6 6"/></symbol>
        <symbol id="icone-externo" viewBox="0 0 24 24"><path d="M14 3h7v7m0-7L10 14m0-10H5a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2h13a2 2 0 0 0 2-2v-5"/></symbol>
        <symbol id="icone-usuario" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21v-2a6 6 0 0 1 6-6h4a6 6 0 0 1 6 6v2"/></symbol>
        <symbol id="icone-usuarios" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3"/><path d="M3 21v-3a5 5 0 0 1 5-5h2a5 5 0 0 1 5 5v3m1-16a3 3 0 0 1 0 6m2 3a5 5 0 0 1 3 4v3"/></symbol>
        <symbol id="icone-documento" viewBox="0 0 24 24"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9l-6-6Zm0 0v6h6M8 13h8m-8 4h5"/></symbol>
        <symbol id="icone-categoria" viewBox="0 0 24 24"><path d="M3 7V4a1 1 0 0 1 1-1h7l10 10-8 8L3 11V7Z"/><circle cx="7.5" cy="7.5" r="1"/></symbol>
        <symbol id="icone-calendario" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18m-13 4h2m4 0h2m-8 3h2"/></symbol>
        <symbol id="icone-escudo" viewBox="0 0 24 24"><path d="m12 3 8 3v6c0 5-8 9-8 9s-8-4-8-9V6l8-3Z"/><path d="m8 12 3 3 5-6"/></symbol>
        <symbol id="icone-sair" viewBox="0 0 24 24"><path d="M9 4H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h4m6-13 5 5-5 5m-7-5h12"/></symbol>
    </svg>

    <header class="painel-topo">
        <div class="painel-container topo-interno">
            <div class="marca">
                <a href="<?= e($caminhoPublico) ?>/index.php" aria-label="Portal Acadêmico SI — página inicial">
                    <span class="marca-sigla">SI</span>
                    <span class="marca-nome">Portal Acadêmico<span class="marca-legenda">SISTEMAS DE INFORMAÇÃO · ESUCRI</span></span>
                </a>
            </div>
            <nav class="painel-navegacao" aria-label="Navegação do administrador">
                <a class="nav-atual" href="dashboard.php" aria-current="page"><?php $icone('painel'); ?> Visão geral</a>
                <a href="<?= e($caminhoPublico) ?>/index.php"
                    target="_blank"
                    rel="noopener noreferrer">
                        Ver portal <?php $icone('externo'); ?>
                </a>
            </nav>
            <a href="logout.php" class="painel-sair"><?php $icone('sair'); ?> Sair</a>
        </div>
    </header>

    <main id="principal">
        <section class="painel-hero" aria-labelledby="titulo-painel">
            <div class="painel-container hero-interno">
                <div class="hero-texto">
                    <span class="hero-etiqueta"><span></span> ÁREA ADMINISTRATIVA</span>
                    <h1 id="titulo-painel">Olá, <span><?= e($nome) ?>.</span></h1>
                    <p>Boas-vindas ao seu espaço de gestão.<br>O próximo capítulo do Portal SI começa aqui.</p>

                    <div class="hero-acoes">
                        <a class="painel-botao" href="<?= e($caminhoPublico) ?>/index.php">Explorar o portal <?php $icone('seta'); ?></a>
                        <a class="hero-link" href="#minha-conta">Minha conta <?php $icone('usuario'); ?></a>
                    </div>
                </div>
                <div class="hero-assinatura" aria-hidden="true">
                    <div class="orbita orbita-externa"></div>
                    <div class="orbita orbita-interna"></div>
                    <div class="assinatura-simbolo">
                        SI<span>CONEXÕES QUE TRANSFORMAM</span>
                    </div>
                    <span class="orbita-ponto ponto-um"></span>
                    <span class="orbita-ponto ponto-dois"></span>
                    <div class="assinatura-selo">
                        <?php $icone('escudo'); ?> Gestão do portal
                    </div>
                </div>
            </div>
        </section>

        <div class="painel-container painel-corpo">
            <div class="contexto-painel"><span>Seu espaço de trabalho</span><span class="contexto-perfil"><?php $icone('escudo'); ?> Administrador</span></div>
            <div class="painel-colunas">
                <section class="gestao" aria-labelledby="titulo-gestao">
                    <div class="secao-titulo"><p class="sobretitulo">GESTÃO DO PORTAL</p><h2 id="titulo-gestao">Tudo em seu lugar.</h2><p>As áreas que vão dar vida ao seu painel.</p></div>

                    <div class="modulos-grade">
                        <?php foreach ($modulos as $modulo): ?>
                            <article class="modulo-card modulo-<?= e($modulo['cor']) ?>">
                                <div class="modulo-topo">
                                    <span class="modulo-icone"><?php $icone($modulo['icone']); ?></span>
                                </div>
                                <h3><?= e($modulo['nome']) ?></h3>
                                <p><?= e($modulo['descricao']) ?></p>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <p class="gestao-nota">Novas ferramentas de gestão serão disponibilizadas neste espaço.</p>
                </section>

                <aside class="conta-coluna" aria-label="Sua conta e acesso ao portal">
                    <section class="conta-card" id="minha-conta" aria-labelledby="titulo-conta">
                        <div class="conta-cabecalho">
                            <span class="sobretitulo">MINHA CONTA</span>
                            <?php $icone('usuario'); ?>
                        </div>

                        <div class="conta-avatar" aria-hidden="true">
                            <?php $icone('usuario'); ?>
                        </div>

                        <h2 id="titulo-conta"><?= e($nome) ?></h2>
                        <span class="perfil-etiqueta"><?php $icone('escudo'); ?> Administrador</span>

                        <dl class="conta-dados"><div><dt>E-mail</dt><dd><?= e($email) ?></dd></div><div><dt>Acesso</dt><dd>Área administrativa</dd></div></dl>
                        <div class="conta-sessao">
                            <span></span> Sessão autenticada
                        </div>
                    </section>
                    <a class="portal-card" href="<?= e($caminhoPublico) ?>/index.php">
                        <span class="portal-card-topo">DO PAINEL PARA A COMUNIDADE <?php $icone('externo'); ?></span>
                        <strong>Veja o portal<br>ganhar vida.</strong>
                        <span>Acompanhe as publicações na página inicial.</span>
                        <span class="portal-card-link">Visitar Portal SI <?php $icone('seta'); ?></span>
                    </a>
                </aside>
            </div>
        </div>
    </main>
    <footer class="painel-rodape painel-container"><span><strong>Portal SI</strong> · Conectando conhecimento e comunidade.</span><span>Sistemas de Informação · ESUCRI</span></footer>
</body>
</html>
