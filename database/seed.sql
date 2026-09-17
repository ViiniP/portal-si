-- ============================================================
-- PORTAL DE COMUNICAÇÃO SI — ESUCRI (PostgreSQL)
-- Arquivo: database/seed.sql
-- Carga inicial: 6 categorias + usuário administrador
-- ============================================================

INSERT INTO categorias (nome, slug, descricao, icone, ordem, ativa) VALUES
('Home', 'home', 'Página inicial e destaques', 'bi-house', 1, TRUE),
('Sobre o Curso', 'sobre-o-curso', 'Informações institucionais e matriz curricular', 'bi-info-circle', 2, TRUE),
('Ações Sociais', 'acoes-sociais', 'Projetos comunitários e intervenções sociais', 'bi-heart', 3, TRUE),
('Atividades Complementares', 'atividades-complementares', 'Normas e eventos de extensão acadêmica', 'bi-journal-check', 4, TRUE),
('Projetos de Extensão', 'projetos-extensao', 'Iniciativas práticas com a comunidade externa', 'bi-mortarboard', 5, TRUE),
('Eventos', 'eventos', 'Congressos, palestras, jornadas e workshops de SI', 'bi-calendar-event', 6, TRUE);

-- Usuário admin inicial — SUBSTITUA o hash gerando com:
-- no PHP: echo password_hash('sua-senha-forte', PASSWORD_DEFAULT);
INSERT INTO usuarios (nome, email, senha_hash, perfil, status) VALUES
('Administrador do Portal', 'admin@esucri.com.br', '$REPLACE_COM_O_HASH_GERADO', 'admin', 'ativo');