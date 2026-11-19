CREATE TABLE IF NOT EXISTS portal_imagens (
    caminho VARCHAR(120) PRIMARY KEY,
    mime VARCHAR(30) NOT NULL CHECK (mime IN ('image/jpeg', 'image/png', 'image/gif')),
    dados BYTEA NOT NULL CHECK (octet_length(dados) BETWEEN 1 AND 2097152),
    criado_em TIMESTAMP NOT NULL DEFAULT NOW()
);
