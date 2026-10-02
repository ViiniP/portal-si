-- ============================================================
-- PORTAL DE COMUNICAÇÃO SI — ESUCRI (PostgreSQL)
-- Arquivo: database/seed.sql
-- Dados iniciais para desenvolvimento e testes
-- Executar APÓS database/schema.sql dentro do banco portal_si
-- ============================================================

BEGIN;

-- ============================================================
-- 1) USUÁRIOS
-- ============================================================
-- IMPORTANTE:
-- As senhas abaixo são hashes de exemplo para popular o banco.
-- Para login real, substitua senha_hash por valores gerados pelo
-- password_hash() do PHP.

INSERT INTO usuarios (nome, email, senha_hash, perfil, status, bio, foto)
VALUES
    (
        'Administrador Portal SI',
        'admin@portal-si.local',
        '$2y$10$abcdefghijklmnopqrstuv12345678901234567890123456789012',
        'admin',
        'ativo',
        'Administrador responsável pela gestão do Portal SI.',
        NULL
    ),
    (
        'Prof. Jucemar Formigoni',
        'jucemar@portal-si.local',
        '$2y$10$abcdefghijklmnopqrstuv12345678901234567890123456789012',
        'editor',
        'ativo',
        'Professor e editor de conteúdos acadêmicos do Portal SI.',
        NULL
    ),
    (
        'Equipe Acadêmica SI',
        'academico@portal-si.local',
        '$2y$10$abcdefghijklmnopqrstuv12345678901234567890123456789012',
        'aluno',
        'ativo',
        'Perfil acadêmico utilizado para publicações de projetos e atividades.',
        NULL
    )
ON CONFLICT (email) DO NOTHING;

-- ============================================================
-- 2) CATEGORIAS
-- ============================================================

INSERT INTO categorias (nome, slug, descricao, icone, ordem, ativa)
VALUES
    (
        'Ações Sociais',
        'acoes-sociais',
        'Projetos e iniciativas de impacto social desenvolvidos pela comunidade acadêmica.',
        'social',
        1,
        TRUE
    ),
    (
        'Extensão',
        'extensao',
        'Projetos de extensão universitária desenvolvidos pelo curso de Sistemas de Informação.',
        'extensao',
        2,
        TRUE
    ),
    (
        'Eventos Acadêmicos',
        'eventos-academicos',
        'Palestras, seminários, workshops e demais eventos acadêmicos.',
        'evento',
        3,
        TRUE
    ),
    (
        'Produção Científica',
        'producao-cientifica',
        'Artigos, pesquisas e produções científicas da comunidade acadêmica.',
        'ciencia',
        4,
        TRUE
    ),
    (
        'Notícias',
        'noticias',
        'Notícias e comunicados relacionados ao curso de Sistemas de Informação.',
        'noticia',
        5,
        TRUE
    )
ON CONFLICT (slug) DO NOTHING;

-- ============================================================
-- 3) CONTEÚDOS
-- ============================================================
-- Os relacionamentos são resolvidos pelos slugs/e-mails para evitar
-- depender de IDs fixos.

INSERT INTO conteudos (
    titulo,
    slug,
    resumo,
    corpo,
    imagem_capa,
    link_youtube,
    categoria_id,
    autor_id,
    status,
    destaque,
    publicado_em
)
SELECT
    'Desenvolvimento de Aplicações para o Terceiro Setor',
    'desenvolvimento-aplicacoes-terceiro-setor',
    'Acadêmicos desenvolvem soluções digitais sob medida para entidades filantrópicas locais.',
    'O projeto aproxima os acadêmicos de Sistemas de Informação das necessidades reais de organizações do terceiro setor, promovendo aprendizado prático, responsabilidade social e desenvolvimento de soluções digitais.',
    'assets/imagens/terceiro-setor.jpg',
    NULL,
    c.id,
    u.id,
    'publicado',
    TRUE,
    TIMESTAMP '2026-09-30 10:00:00'
FROM categorias c
CROSS JOIN usuarios u
WHERE c.slug = 'extensao'
  AND u.email = 'jucemar@portal-si.local'
ON CONFLICT (slug) DO NOTHING;

INSERT INTO conteudos (
    titulo, slug, resumo, corpo, imagem_capa, link_youtube,
    categoria_id, autor_id, status, destaque, publicado_em
)
SELECT
    'Tecnologia e Transformação Social',
    'tecnologia-transformacao-social',
    'Projeto acadêmico utiliza tecnologia para apoiar iniciativas sociais da comunidade regional.',
    'A iniciativa demonstra como a tecnologia pode contribuir para organizações e projetos sociais, aproximando universidade e comunidade por meio de ações extensionistas.',
    'assets/imagens/transformacao-social.jpg',
    NULL,
    c.id,
    u.id,
    'publicado',
    FALSE,
    TIMESTAMP '2026-09-28 14:30:00'
FROM categorias c
CROSS JOIN usuarios u
WHERE c.slug = 'acoes-sociais'
  AND u.email = 'academico@portal-si.local'
ON CONFLICT (slug) DO NOTHING;

INSERT INTO conteudos (
    titulo, slug, resumo, corpo, imagem_capa, link_youtube,
    categoria_id, autor_id, status, destaque, publicado_em
)
SELECT
    'Semana Acadêmica de Sistemas de Informação',
    'semana-academica-sistemas-informacao',
    'Programação reúne palestras, experiências profissionais e atividades voltadas à área de tecnologia.',
    'A Semana Acadêmica promove integração entre estudantes, professores e profissionais, oferecendo atividades relacionadas ao mercado e à formação em Sistemas de Informação.',
    'assets/imagens/semana-academica.jpg',
    NULL,
    c.id,
    u.id,
    'publicado',
    TRUE,
    TIMESTAMP '2026-09-25 19:00:00'
FROM categorias c
CROSS JOIN usuarios u
WHERE c.slug = 'eventos-academicos'
  AND u.email = 'jucemar@portal-si.local'
ON CONFLICT (slug) DO NOTHING;

INSERT INTO conteudos (
    titulo, slug, resumo, corpo, imagem_capa, link_youtube,
    categoria_id, autor_id, status, destaque, publicado_em
)
SELECT
    'Pesquisa Aplicada em Sistemas de Informação',
    'pesquisa-aplicada-sistemas-informacao',
    'Estudantes apresentam resultados de pesquisas desenvolvidas durante o semestre.',
    'As produções acadêmicas exploram diferentes áreas da computação e demonstram a aplicação dos conhecimentos desenvolvidos ao longo do curso.',
    'assets/imagens/pesquisa-aplicada.jpg',
    NULL,
    c.id,
    u.id,
    'publicado',
    FALSE,
    TIMESTAMP '2026-09-22 09:15:00'
FROM categorias c
CROSS JOIN usuarios u
WHERE c.slug = 'producao-cientifica'
  AND u.email = 'academico@portal-si.local'
ON CONFLICT (slug) DO NOTHING;

INSERT INTO conteudos (
    titulo, slug, resumo, corpo, imagem_capa, link_youtube,
    categoria_id, autor_id, status, destaque, publicado_em
)
SELECT
    'Portal SI ganha nova interface',
    'portal-si-nova-interface',
    'Portal acadêmico recebe uma nova interface responsiva para facilitar o acesso às publicações do curso.',
    'A nova interface do Portal SI foi estruturada com HTML semântico, CSS responsivo e integração com PostgreSQL, priorizando organização, legibilidade e acesso aos conteúdos acadêmicos.',
    'assets/imagens/portal-si.jpg',
    NULL,
    c.id,
    u.id,
    'publicado',
    FALSE,
    TIMESTAMP '2026-09-20 11:00:00'
FROM categorias c
CROSS JOIN usuarios u
WHERE c.slug = 'noticias'
  AND u.email = 'jucemar@portal-si.local'
ON CONFLICT (slug) DO NOTHING;

-- Conteúdo de teste que NÃO aparecerá na Home porque está em rascunho.
INSERT INTO conteudos (
    titulo, slug, resumo, corpo, categoria_id, autor_id,
    status, destaque, publicado_em
)
SELECT
    'Conteúdo em preparação',
    'conteudo-em-preparacao',
    'Exemplo de conteúdo ainda não publicado.',
    'Este registro existe para testar a separação entre rascunhos e publicações disponíveis ao público.',
    c.id,
    u.id,
    'rascunho',
    FALSE,
    NULL
FROM categorias c
CROSS JOIN usuarios u
WHERE c.slug = 'noticias'
  AND u.email = 'academico@portal-si.local'
ON CONFLICT (slug) DO NOTHING;

-- ============================================================
-- 4) TAGS
-- ============================================================

INSERT INTO tags (nome, slug)
VALUES
    ('Extensão', 'extensao'),
    ('Tecnologia', 'tecnologia'),
    ('Comunidade', 'comunidade'),
    ('Pesquisa', 'pesquisa'),
    ('Inovação', 'inovacao'),
    ('Eventos', 'eventos')
ON CONFLICT DO NOTHING;

-- ============================================================
-- 5) RELACIONAMENTO CONTEÚDO x TAGS
-- ============================================================

INSERT INTO conteudo_tags (conteudo_id, tag_id)
SELECT c.id, t.id
FROM conteudos c
JOIN tags t ON t.slug IN ('extensao', 'tecnologia', 'comunidade')
WHERE c.slug = 'desenvolvimento-aplicacoes-terceiro-setor'
ON CONFLICT DO NOTHING;

INSERT INTO conteudo_tags (conteudo_id, tag_id)
SELECT c.id, t.id
FROM conteudos c
JOIN tags t ON t.slug IN ('tecnologia', 'comunidade')
WHERE c.slug = 'tecnologia-transformacao-social'
ON CONFLICT DO NOTHING;

INSERT INTO conteudo_tags (conteudo_id, tag_id)
SELECT c.id, t.id
FROM conteudos c
JOIN tags t ON t.slug IN ('eventos', 'tecnologia')
WHERE c.slug = 'semana-academica-sistemas-informacao'
ON CONFLICT DO NOTHING;

INSERT INTO conteudo_tags (conteudo_id, tag_id)
SELECT c.id, t.id
FROM conteudos c
JOIN tags t ON t.slug IN ('pesquisa', 'inovacao')
WHERE c.slug = 'pesquisa-aplicada-sistemas-informacao'
ON CONFLICT DO NOTHING;

-- ============================================================
-- 6) EVENTOS
-- ============================================================

INSERT INTO eventos (
    titulo, descricao, local, data_inicio, data_fim,
    categoria_id, autor_id, status
)
SELECT
    'Semana Acadêmica de Sistemas de Informação',
    'Encontro acadêmico com palestras e atividades voltadas à tecnologia e ao mercado profissional.',
    'Faculdades ESUCRI',
    TIMESTAMP '2026-10-15 19:00:00',
    TIMESTAMP '2026-10-15 22:00:00',
    c.id,
    u.id,
    'publicado'
FROM categorias c
CROSS JOIN usuarios u
WHERE c.slug = 'eventos-academicos'
  AND u.email = 'jucemar@portal-si.local'
  AND NOT EXISTS (
      SELECT 1
      FROM eventos e
      WHERE e.titulo = 'Semana Acadêmica de Sistemas de Informação'
        AND e.data_inicio = TIMESTAMP '2026-10-15 19:00:00'
  );

INSERT INTO eventos (
    titulo, descricao, local, data_inicio, data_fim,
    categoria_id, autor_id, status
)
SELECT
    'Mostra de Projetos de Extensão',
    'Apresentação de projetos desenvolvidos pelos acadêmicos ao longo do semestre.',
    'Faculdades ESUCRI',
    TIMESTAMP '2026-11-05 19:00:00',
    TIMESTAMP '2026-11-05 21:30:00',
    c.id,
    u.id,
    'publicado'
FROM categorias c
CROSS JOIN usuarios u
WHERE c.slug = 'extensao'
  AND u.email = 'jucemar@portal-si.local'
  AND NOT EXISTS (
      SELECT 1
      FROM eventos e
      WHERE e.titulo = 'Mostra de Projetos de Extensão'
        AND e.data_inicio = TIMESTAMP '2026-11-05 19:00:00'
  );

-- ============================================================
-- 7) MÍDIAS
-- ============================================================

INSERT INTO midias (nome_arquivo, caminho, tipo, tamanho_kb, enviado_por)
SELECT
    'portal-si.jpg',
    'assets/imagens/portal-si.jpg',
    'image/jpeg',
    420,
    u.id
FROM usuarios u
WHERE u.email = 'jucemar@portal-si.local'
  AND NOT EXISTS (
      SELECT 1 FROM midias m WHERE m.caminho = 'assets/imagens/portal-si.jpg'
  );

INSERT INTO midias (nome_arquivo, caminho, tipo, tamanho_kb, enviado_por)
SELECT
    'terceiro-setor.jpg',
    'assets/imagens/terceiro-setor.jpg',
    'image/jpeg',
    380,
    u.id
FROM usuarios u
WHERE u.email = 'jucemar@portal-si.local'
  AND NOT EXISTS (
      SELECT 1 FROM midias m WHERE m.caminho = 'assets/imagens/terceiro-setor.jpg'
  );

-- ============================================================
-- 8) AUDITORIA
-- ============================================================

INSERT INTO auditoria (
    usuario_id, acao, tabela, registro_id, detalhes, ip_origem
)
SELECT
    u.id,
    'SEED_INICIAL',
    'sistema',
    NULL,
    'Carga inicial de dados para desenvolvimento do Portal SI.',
    '127.0.0.1'
FROM usuarios u
WHERE u.email = 'admin@portal-si.local'
  AND NOT EXISTS (
      SELECT 1
      FROM auditoria a
      WHERE a.acao = 'SEED_INICIAL'
        AND a.detalhes = 'Carga inicial de dados para desenvolvimento do Portal SI.'
  );

COMMIT;

-- ============================================================
-- CONSULTAS DE CONFERÊNCIA
-- ============================================================
-- Execute separadamente se quiser conferir os dados:
--
-- SELECT * FROM usuarios ORDER BY id;
-- SELECT * FROM categorias ORDER BY ordem;
-- SELECT id, titulo, status, publicado_em FROM conteudos ORDER BY publicado_em DESC NULLS LAST;
-- SELECT * FROM tags ORDER BY nome;
-- SELECT * FROM eventos ORDER BY data_inicio;
--
-- Consulta equivalente à Home usando o schema atual:
--
-- SELECT
--     c.id,
--     c.titulo,
--     c.slug,
--     c.resumo,
--     c.imagem_capa,
--     c.publicado_em,
--     cat.nome AS categoria_nome,
--     cat.slug AS categoria_slug,
--     u.nome AS autor_nome
-- FROM conteudos c
-- INNER JOIN categorias cat ON c.categoria_id = cat.id
-- INNER JOIN usuarios u ON c.autor_id = u.id
-- WHERE c.status = 'publicado'
--   AND c.publicado_em IS NOT NULL
-- ORDER BY c.publicado_em DESC
-- LIMIT 9;
