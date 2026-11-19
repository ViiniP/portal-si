<?php
declare(strict_types=1);
$arquivo = $_GET['arquivo'] ?? '';
if (!is_string($arquivo) || !preg_match('/^conteudo_[a-f0-9]{32}\.(?:jpg|png|gif)$/D', $arquivo)) {
    http_response_code(404);
    exit;
}
require __DIR__ . '/../config/database.php';
$stmt = $pdo->prepare("SELECT mime, encode(dados, 'base64') AS dados FROM portal_imagens WHERE caminho = :caminho");
$stmt->execute(['caminho' => 'db/' . $arquivo]);
$imagem = $stmt->fetch();
if (!$imagem) { http_response_code(404); exit; }
$dados = base64_decode($imagem['dados'], true);
header('Content-Type: ' . $imagem['mime']);
header('Content-Length: ' . strlen($dados));
header('X-Content-Type-Options: nosniff');
header('Cache-Control: public, max-age=3600');
echo $dados;
