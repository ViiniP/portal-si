<?php

declare(strict_types=1);

class Usuario
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function buscarPorEmail(string $email): ?array
    {
        $sql = "
            SELECT id, nome, email, senha_hash, perfil, status
            FROM usuarios
            WHERE email = :email
              AND status = 'ativo'
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        return $usuario ?: null;
    }

    public function cadastrar(array $dados): bool
    {
        $sql = "
            INSERT INTO usuarios
                (nome, email, senha_hash, perfil, status)
            VALUES
                (:nome, :email, :senha_hash, :perfil, :status)
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'nome' => $dados['nome'],
            'email' => $dados['email'],
            'senha_hash' => password_hash(
                $dados['senha'],
                PASSWORD_DEFAULT
            ),
            'perfil' => $dados['perfil'] ?? 'aluno',
            'status' => 'ativo',
        ]);
    }

    public function listar(): array
    {
        $sql = "
            SELECT
                id,
                nome,
                email,
                perfil,
                status,
                criado_em
            FROM usuarios
            ORDER BY nome ASC, id ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}