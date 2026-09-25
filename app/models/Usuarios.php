<?php
/** Finalidade: Encapsulamento de operações de dados para a entidade 'usuarios'. **/
declare(strict_types=1);
class Usuario { private PDO $pdo;
public function __construct(PDO $pdo)
{
$this-&gt;pdo = $pdo;
}
public function buscarPorEmail(string $email): ?array
{
$sql = &quot;SELECT id, nome, email, senha_hash, perfil, status
FROM usuarios
WHERE email = :email AND status = &#39;ativo&#39;
LIMIT 1&quot;;
$stmt = $this-&gt;pdo-&gt;prepare($sql);
$stmt-&gt;bindValue(&#39;:email&#39;, $email, PDO::PARAM_STR);
$stmt-&gt;execute();
$usuario = $stmt-&gt;fetch();
return $usuario ?: null;
}
public function cadastrar(array $dados): bool
{
$sql = &quot;INSERT INTO usuarios (nome, email, senha_hash, perfil, status)
VALUES (:nome, :email, :senha_hash, :perfil, :status)&quot;;
$stmt = $this-&gt;pdo-&gt;prepare($sql);
$hashSeguro = password_hash($dados[&#39;senha&#39;], PASSWORD_DEFAULT);
$stmt-&gt;bindValue(&#39;:nome&#39;, $dados[&#39;nome&#39;], PDO::PARAM_STR);
$stmt-&gt;bindValue(&#39;:email&#39;, $dados[&#39;email&#39;], PDO::PARAM_STR);
$stmt-&gt;bindValue(&#39;:senha_hash&#39;, $hashSeguro, PDO::PARAM_STR);
$stmt-&gt;bindValue(&#39;:perfil&#39;, $dados[&#39;perfil&#39;] ?? &#39;aluno&#39;, PDO::PARAM_STR);
$stmt-&gt;bindValue(&#39;:status&#39;, &#39;ativo&#39;, PDO::PARAM_STR);
return $stmt-&gt;execute();
}
}?>