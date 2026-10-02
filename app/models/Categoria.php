<?php

// Arquivo: app/models/Categoria.php

class Categoria
{
    private PDO $db;

    public function __construct(PDO $conexao)
    {
        $this->db = $conexao;
    }

    /**
     * Recupera todas as categorias ativas ordenadas
     * para construção de menus.
     *
     * @return array
     */
    public function listarAtivas(): array
    {
        $sql = "SELECT id, nome, slug, icone
                FROM categorias
                WHERE ativa = TRUE
                ORDER BY ordem ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}