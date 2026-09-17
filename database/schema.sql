-- ============================================================
-- PORTAL DE COMUNICAÇÃO SI — ESUCRI (PostgreSQL)
-- Arquivo: database/schema.sql
-- Disciplina: Projeto de Extensão IV | Aula 6 (10/09/2026)
-- Executar dentro do banco: portal_si
-- ============================================================

-- Função auxiliar: atualiza o campo atualizado_em automaticamente
CREATE OR REPLACE FUNCTION set_atualizado_em()
RETURNS TRIGGER AS $$
BEGIN
    NEW.atualizado_em = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- 1) USUARIOS -------------------------------------------------
CREATE TABLE usuarios (
    id            SERIAL PRIMARY KEY,
    nome          VARCHAR(120) NOT NULL,
    email         VARCHAR(160) NOT NULL UNIQUE,
    senha_hash    TEXT NOT NULL,
    perfil        VARCHAR(20) NOT NULL DEFAULT 'aluno'
                  CHECK (perfil IN ('admin','editor','aluno')),
    status        VARCHAR(20) NOT NULL DEFAULT 'ativo'
                  CHECK (status IN ('ativo','inativo','pendente')),
    bio           TEXT,
    foto          VARCHAR(255),
    criado_em     TIMESTAMP NOT NULL DEFAULT NOW(),
    atualizado_em TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_usuarios_email  ON usuarios(email);
CREATE INDEX idx_usuarios_perfil ON usuarios(perfil);

CREATE TRIGGER trg_usuarios_atualizado
BEFORE UPDATE ON usuarios
FOR EACH ROW EXECUTE FUNCTION set_atualizado_em();

-- 2) CATEGORIAS ------------------------------------------------
CREATE TABLE categorias (
    id          SERIAL PRIMARY KEY,
    nome        VARCHAR(80) NOT NULL,
    slug        VARCHAR(100) NOT NULL UNIQUE,
    descricao   TEXT,
    icone       VARCHAR(50),
    ordem       INT NOT NULL DEFAULT 0,
    ativa       BOOLEAN NOT NULL DEFAULT TRUE,
    criado_em   TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_categorias_slug ON categorias(slug);
CREATE INDEX idx_categorias_ativa ON categorias(ativa);

-- 3) CONTEUDOS --------------------------------------------------
CREATE TABLE conteudos (
    id              SERIAL PRIMARY KEY,
    titulo          VARCHAR(200) NOT NULL,
    slug            VARCHAR(220) NOT NULL UNIQUE,
    resumo          TEXT,
    corpo           TEXT,
    imagem_capa     VARCHAR(255),
    link_youtube    VARCHAR(255),
    categoria_id    INT NOT NULL REFERENCES categorias(id) ON DELETE RESTRICT,
    autor_id        INT NOT NULL REFERENCES usuarios(id) ON DELETE RESTRICT,
    status          VARCHAR(20) NOT NULL DEFAULT 'rascunho'
                    CHECK (status IN ('rascunho','revisao','publicado','arquivado')),
    destaque        BOOLEAN NOT NULL DEFAULT FALSE,
    publicado_em    TIMESTAMP,
    criado_em       TIMESTAMP NOT NULL DEFAULT NOW(),
    atualizado_em   TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_conteudos_categoria ON conteudos(categoria_id);
CREATE INDEX idx_conteudos_autor     ON conteudos(autor_id);
CREATE INDEX idx_conteudos_status    ON conteudos(status);
CREATE INDEX idx_conteudos_publicado ON conteudos(publicado_em DESC);

CREATE TRIGGER trg_conteudos_atualizado
BEFORE UPDATE ON conteudos
FOR EACH ROW EXECUTE FUNCTION set_atualizado_em();

-- 4) TAGS ------------------------------------------------------
CREATE TABLE tags (
    id        SERIAL PRIMARY KEY,
    nome      VARCHAR(60) NOT NULL UNIQUE,
    slug      VARCHAR(70) NOT NULL UNIQUE,
    criado_em TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_tags_slug ON tags(slug);

-- 5) CONTEUDO_TAGS (N:N) ---------------------------------------
CREATE TABLE conteudo_tags (
    conteudo_id INT NOT NULL REFERENCES conteudos(id) ON DELETE CASCADE,
    tag_id      INT NOT NULL REFERENCES tags(id) ON DELETE CASCADE,
    PRIMARY KEY (conteudo_id, tag_id)
);

CREATE INDEX idx_conteudo_tags_tag ON conteudo_tags(tag_id);

-- 6) EVENTOS ----------------------------------------------------
CREATE TABLE eventos (
    id           SERIAL PRIMARY KEY,
    titulo       VARCHAR(200) NOT NULL,
    descricao    TEXT,
    local        VARCHAR(160),
    data_inicio  TIMESTAMP NOT NULL,
    data_fim     TIMESTAMP,
    categoria_id INT REFERENCES categorias(id) ON DELETE SET NULL,
    autor_id     INT NOT NULL REFERENCES usuarios(id) ON DELETE RESTRICT,
    status       VARCHAR(20) NOT NULL DEFAULT 'publicado'
                 CHECK (status IN ('publicado','cancelado','rascunho')),
    criado_em    TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_eventos_data ON eventos(data_inicio);
CREATE INDEX idx_eventos_autor ON eventos(autor_id);

-- 7) MIDIAS -----------------------------------------------------
CREATE TABLE midias (
    id           SERIAL PRIMARY KEY,
    nome_arquivo VARCHAR(255) NOT NULL,
    caminho      VARCHAR(255) NOT NULL,
    tipo         VARCHAR(100),
    tamanho_kb   INT,
    enviado_por  INT REFERENCES usuarios(id) ON DELETE SET NULL,
    criado_em    TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_midias_enviado_por ON midias(enviado_por);

-- 8) AUDITORIA --------------------------------------------------
CREATE TABLE auditoria (
    id          BIGSERIAL PRIMARY KEY,
    usuario_id  INT REFERENCES usuarios(id) ON DELETE SET NULL,
    acao        VARCHAR(50) NOT NULL,
    tabela      VARCHAR(50),
    registro_id INT,
    detalhes    TEXT,
    ip_origem   VARCHAR(45),
    criado_em   TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_auditoria_usuario ON auditoria(usuario_id);
CREATE INDEX idx_auditoria_data    ON auditoria(criado_em DESC);