<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';

function validarConteudo(array $dados, array $categorias): array
{
    $erros = [];
    if ($dados['titulo'] === '' || mb_strlen($dados['titulo']) > 200) $erros[] = 'Informe um título com até 200 caracteres.';
    if (!in_array((int) $dados['categoria_id'], array_map('intval', array_column($categorias, 'id')), true)) $erros[] = 'Selecione uma categoria disponível.';
    if ($dados['corpo'] === '') $erros[] = 'Escreva o texto da publicação.';
    if (mb_strlen($dados['resumo']) > 500) $erros[] = 'O resumo deve ter até 500 caracteres.';
    if (!youtubeValido($dados['link_youtube'])) $erros[] = 'Informe um link HTTPS do YouTube válido ou deixe o campo vazio.';
    $permitidos = podePublicar() ? ['rascunho', 'revisao', 'publicado', 'arquivado'] : ['rascunho', 'revisao'];
    if (!in_array($dados['status'], $permitidos, true)) $erros[] = 'Selecione uma situação permitida para seu perfil.';
    return $erros;
}

function salvarImagem(array $arquivo): string
{
    if (($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) throw new RuntimeException('Não foi possível enviar a imagem. Verifique o limite de 2 MB.');
    if (($arquivo['size'] ?? 0) > 2 * 1024 * 1024) throw new RuntimeException('A imagem deve ter no máximo 2 MB.');
    $tmp = $arquivo['tmp_name'] ?? '';
    if (!is_string($tmp) || !is_uploaded_file($tmp)) throw new RuntimeException('Envio de imagem inválido.');
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp);
    $tipos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];
    if (!isset($tipos[$mime]) || @getimagesize($tmp) === false) throw new RuntimeException('Selecione uma imagem JPG, PNG ou GIF válida.');
    $dir = __DIR__ . '/../views/admin/uploads';
    if (!is_dir($dir) && !mkdir($dir, 0755, true)) throw new RuntimeException('Não foi possível preparar a pasta de imagens.');
    $caminho = 'uploads/conteudo_' . bin2hex(random_bytes(16)) . '.' . $tipos[$mime];
    if (!move_uploaded_file($tmp, __DIR__ . '/../views/admin/' . $caminho)) throw new RuntimeException('Não foi possível salvar a imagem.');
    return $caminho;
}

function removerImagem(?string $caminho): void
{
    if ($caminho && preg_match('~^uploads/conteudo_[a-zA-Z0-9_.-]+\\.(jpg|png|gif)$~', $caminho)) {
        $arquivo = __DIR__ . '/../views/admin/' . $caminho;
        if (is_file($arquivo) && !unlink($arquivo)) error_log('Não foi possível remover imagem: ' . $caminho);
    }
}
