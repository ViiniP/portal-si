<?php

// Arquivo: app/models/Conteudo.php

class Conteudo
{
    private PDO $db;

    public function __construct(PDO $conexao)
    {
        $this->db = $conexao;
    }

    /**
     * Extrai publicações ativas com resolução de relacionamentos externos.
     *
     * @param int $limite Quantidade máxima de registros retornados.
     * @return array
     */
    public function listarPublicados(int $limite = 6): array
    {
        $sql = "SELECT
                    c.id,
                    c.titulo,
                    c.slug,
                    c.resumo,
                    c.imagem_capa,
                    c.publicado_em,
                    cat.nome AS categoria_nome,
                    cat.slug AS categoria_slug,
                    u.nome AS autor_nome
                FROM conteudos c
                INNER JOIN categorias cat
                    ON c.categoria_id = cat.id
                INNER JOIN usuarios u
                    ON c.autor_id = u.id
                WHERE c.status = 'publicado'
                    AND c.publicado_em IS NOT NULL
                ORDER BY c.publicado_em DESC
                LIMIT :limite";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}