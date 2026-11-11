<?php
declare(strict_types=1);

class Usuario
{
    public function __construct(private PDO $pdo) {}

    public function buscarPorEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare("SELECT id, nome, email, senha_hash, perfil, status FROM usuarios WHERE LOWER(email) = LOWER(:email) AND status = 'ativo' LIMIT 1");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function cadastrar(array $dados): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO usuarios (nome, email, senha_hash, perfil, status) VALUES (:nome, :email, :senha_hash, 'aluno', 'ativo')");
        return $stmt->execute([
            'nome' => $dados['nome'], 'email' => strtolower($dados['email']),
            'senha_hash' => password_hash($dados['senha'], PASSWORD_DEFAULT),
        ]);
    }
}
