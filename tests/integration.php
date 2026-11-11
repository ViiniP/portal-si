<?php
/**
 * Testes HTTP reais em schema PostgreSQL descartável.
 * Uso: C:\xampp\php\php.exe tests/integration.php [--visual]
 * --visual mantém o servidor até existir tests/.runtime/stop.
 */
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
foreach (['pdo_pgsql', 'curl', 'mbstring', 'fileinfo'] as $ext) {
    if (!extension_loaded($ext)) { fwrite(STDERR, "Extensão obrigatória: $ext\n"); exit(1); }
}
$root = dirname(__DIR__);
$runtime = __DIR__ . '/.runtime';
if (!is_dir($runtime)) mkdir($runtime, 0755, true);
$schema = 'portal_si_test_' . bin2hex(random_bytes(6));
$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '5432';
$dbname = getenv('DB_NAME') ?: 'portal_si';
$pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname;connect_timeout=5", getenv('DB_USER') ?: 'postgres', getenv('DB_PASSWORD') ?: 'postgres', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
$server = null;
$checks = 0;
$failures = 0;
$files = [];
$base = 'http://127.0.0.1:8765';
$adminPath = '/app/views/admin/';
$cookieFiles = [];
$httpBodies = [];

function check(bool $condition, string $label): void
{
    global $checks, $failures;
    $checks++;
    if (!$condition) { $failures++; echo "FALHOU: $label\n"; }
    else echo "OK: $label\n";
}
function request(string $path, ?array $data = null, string $client = 'guest', bool $multipart = false): array
{
    global $base, $runtime, $cookieFiles, $httpBodies;
    $cookieFiles[$client] ??= $runtime . '/' . $client . '.cookies';
    $curl = curl_init($base . $path);
    curl_setopt_array($curl, [CURLOPT_RETURNTRANSFER => true, CURLOPT_HEADER => true, CURLOPT_FOLLOWLOCATION => false, CURLOPT_TIMEOUT => 10, CURLOPT_COOKIEJAR => $cookieFiles[$client], CURLOPT_COOKIEFILE => $cookieFiles[$client]]);
    if ($data !== null) {
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $multipart ? $data : http_build_query($data));
    }
    $raw = curl_exec($curl);
    if ($raw === false) throw new RuntimeException(curl_error($curl));
    $size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $headers = substr($raw, 0, $size);
    $body = substr($raw, $size);
    preg_match('/^Location: (.+)$/mi', $headers, $location);
    $result = ['status' => $status, 'body' => $body, 'headers' => $headers, 'location' => trim($location[1] ?? '')];
    $httpBodies[] = $body;
    return $result;
}
function token(string $path, string $client): string
{
    $page = request($path, null, $client);
    if (!preg_match('/name="csrf" value="([a-f0-9]+)"/', $page['body'], $matches)) throw new RuntimeException('Token não encontrado em ' . $path);
    return $matches[1];
}
function login(string $client, string $email, string $password): array
{
    return request('/app/views/admin/login.php', ['csrf' => token('/app/views/admin/login.php', $client), 'email' => $email, 'senha' => $password], $client);
}
function bodyContains(array $response, string $text): bool { return str_contains($response['body'], $text); }

try {
    // Apenas o schema criado nesta execução é alterado ou removido.
    $pdo->exec('CREATE SCHEMA "' . $schema . '"');
    $pdo->exec('SET search_path TO "' . $schema . '"');
    $pdo->exec(file_get_contents($root . '/database/schema.sql'));
    $pdo->exec(file_get_contents($root . '/database/seed.sql'));
    check($pdo->query('SELECT current_schema()')->fetchColumn() === $schema, 'Banco de teste isolado do schema original');
    $env = getenv();
    $env['PGOPTIONS'] = '-c search_path=' . $schema;
    $socket = @stream_socket_client('tcp://127.0.0.1:8765', $errno, $error, .3);
    if ($socket) { fclose($socket); throw new RuntimeException('Porta 8765 ocupada; encerre o servidor de teste anterior.'); }
    $server = proc_open([PHP_BINARY, '-d', 'display_errors=1', '-d', 'log_errors=1', '-S', '127.0.0.1:8765', '-t', $root], [0 => ['pipe', 'r'], 1 => ['file', $runtime . '/server.log', 'w'], 2 => ['file', $runtime . '/server.log', 'a']], $pipes, $root, $env, ['bypass_shell' => true]);
    if (!is_resource($server)) throw new RuntimeException('Servidor não iniciou.');
    fclose($pipes[0]);
    for ($i = 0; $i < 30; $i++) {
        $socket = @stream_socket_client('tcp://127.0.0.1:8765', $errno, $error, .2);
        if ($socket) { fclose($socket); break; }
        usleep(100000);
    }
    $home = request('/public/index.php');
    check($home['status'] === 200 && bodyContains($home, 'Desenvolvimento de Aplicações'), 'Página inicial carrega publicações do PostgreSQL');
    check(!bodyContains($home, 'Conteúdo em preparação'), 'Rascunho não aparece na página pública');
    check(request('/')['location'] === 'public/index.php', 'Entrada do projeto redireciona para o portal');
    $category = request('/public/categoria.php?slug=extensao');
    check($category['status'] === 200 && bodyContains($category, 'Desenvolvimento de Aplicações') && !bodyContains($category, 'Portal SI ganha nova interface'), 'Categoria filtra conteúdos corretamente');
    check(request('/public/categoria.php?slug=inexistente')['status'] === 404, 'Categoria inexistente retorna 404');
    check(request('/public/conteudo.php?slug=inexistente')['status'] === 404, 'Publicação inexistente retorna 404');
    check(request('/public/conteudo.php?slug=conteudo-em-preparacao')['status'] === 404, 'Rascunho não pode ser aberto por URL');
    foreach (['index.php', 'conteudo_listar.php', 'conteudo_cadastrar.php', 'conteudos_editar.php?id=1', 'conteudos_excluir.php?id=1'] as $path) {
        $r = request($adminPath . $path);
        check($r['status'] === 302 && $r['location'] === 'login.php', 'Acesso anônimo bloqueado: ' . $path);
    }
    $registration = $adminPath . 'cadastro_usuario.php';
    check(request($registration, ['nome'=>'Teste', 'email'=>'teste@example.test'])['status'] === 403, 'Cadastro rejeita envio sem CSRF');
    $csrf = token($registration, 'student');
    $r = request($registration, ['csrf'=>$csrf,'nome'=>'X','email'=>'invalido','senha'=>'123','senha_confirmacao'=>'456'], 'student');
    check(bodyContains($r, 'Informe um nome') && bodyContains($r, 'Informe um e-mail') && bodyContains($r, 'As senhas'), 'Cadastro valida nome, e-mail e senhas');
    $r = request($registration, ['csrf'=>$csrf,'nome'=>'Aluno de Teste','email'=>'nulo@example.test','senha'=>"Senha\0Teste2026",'senha_confirmacao'=>"Senha\0Teste2026"], 'student');
    check(bodyContains($r, 'caractere inválido'), 'Cadastro rejeita caractere nulo sem erro fatal no hash');
    $r = request($registration, ['csrf'=>$csrf,'nome'=>'Aluno de Teste','email'=>'ALUNO.TESTE@example.test','senha'=>'TesteSeguro@2026','senha_confirmacao'=>'TesteSeguro@2026','perfil'=>'admin'], 'student');
    check($r['status'] === 302 && $r['location'] === 'login.php?sucesso=cadastrado', 'Cadastro válido redireciona ao login');
    $student = $pdo->query("SELECT * FROM usuarios WHERE email='aluno.teste@example.test'")->fetch();
    check($student && $student['perfil'] === 'aluno' && password_verify('TesteSeguro@2026', $student['senha_hash']), 'Cadastro normaliza e-mail, protege perfil e grava hash da senha');
    $r = request($registration, ['csrf'=>$csrf,'nome'=>'Aluno Duplicado','email'=>'ALUNO.TESTE@example.test','senha'=>'TesteSeguro@2026','senha_confirmacao'=>'TesteSeguro@2026'], 'student');
    check(bodyContains($r, 'já possui cadastro'), 'Cadastro impede e-mail duplicado com diferença de caixa');
    check(bodyContains(login('student', 'aluno.teste@example.test', 'senha-errada'), 'E-mail ou senha inválidos'), 'Login recusa senha incorreta');
    check(bodyContains(login('guest', "' OR 1=1 --@example.test", 'qualquer'), 'Informe seu e-mail'), 'Login recusa entrada inválida');
    check(login('student', 'ALUNO.TESTE@example.test', 'TesteSeguro@2026')['status'] === 302, 'Login aceita e-mail sem distinção entre maiúsculas e minúsculas');
    check(login('admin', 'admin@portal-si.local', 'PortalSI@2026')['status'] === 302, 'Conta administrativa do seed autentica');
    check(login('editor', 'jucemar@portal-si.local', 'PortalSI@2026')['status'] === 302, 'Conta de editor do seed autentica');
    $dashboard = request($adminPath . 'index.php', null, 'admin');
    check($dashboard['status'] === 200 && bodyContains($dashboard, 'Atividade recente'), 'Painel exibe indicadores reais');
    check(str_contains($dashboard['headers'], 'text/html'), 'Resposta do painel é HTML');
    $newPath = $adminPath . 'conteudo_cadastrar.php';
    $csrfAdmin = token($newPath, 'admin');
    $cat = (int) $pdo->query("SELECT id FROM categorias WHERE slug='extensao'")->fetchColumn();
    $payload = ['csrf'=>$csrfAdmin,'titulo'=>'Teste de integração <script>alert(1)</script>','categoria_id'=>$cat,'resumo'=>'Resumo seguro <b>texto</b>','corpo'=>"Primeiro parágrafo.\n\nSegundo parágrafo <script>alert(1)</script>",'link_youtube'=>'https://www.youtube.com/watch?v=teste','status'=>'publicado'];
    check(request($newPath, array_merge($payload, ['csrf'=>'invalido']), 'admin')['status'] === 403, 'Publicação rejeita token CSRF inválido');
    $r = request($newPath, array_merge($payload, ['categoria_id'=>99999,'link_youtube'=>'javascript:alert(1)']), 'admin');
    check(bodyContains($r, 'categoria disponível') && bodyContains($r, 'link HTTPS'), 'Publicação valida categoria e URL de vídeo');
    $fake = $runtime . '/fake.png';
    file_put_contents($fake, '<?php echo "invalido";');
    $files[] = $fake;
    $r = request($newPath, array_merge($payload, ['imagem'=>new CURLFile($fake, 'image/png', 'fake.png')]), 'admin', true);
    check(bodyContains($r, 'imagem JPG, PNG ou GIF válida'), 'Upload rejeita arquivo executável disfarçado de imagem');
    $big = $runtime . '/big.png'; file_put_contents($big, str_repeat('x', 2 * 1024 * 1024 + 1)); $files[] = $big;
    $r = request($newPath, array_merge($payload, ['imagem'=>new CURLFile($big, 'image/png', 'big.png')]), 'admin', true);
    check(bodyContains($r, '2 MB'), 'Upload rejeita imagem acima de 2 MB');
    $image = $runtime . '/test.png';
    file_put_contents($image, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jRZkAAAAASUVORK5CYII='));
    $files[] = $image;
    $r = request($newPath, array_merge($payload, ['imagem'=>new CURLFile($image, 'image/png', 'test.png')]), 'admin', true);
    check($r['status'] === 302 && $r['location'] === 'conteudo_listar.php?sucesso=salvo', 'Criação com capa e publicação conclui e redireciona');
    $created = $pdo->query("SELECT * FROM conteudos WHERE titulo LIKE 'Teste de integração%' ORDER BY id DESC LIMIT 1")->fetch();
    if (!$created) throw new RuntimeException('Conteúdo de teste não foi criado.');
    $id = (int) $created['id'];
    $files[] = $root . '/app/views/admin/' . $created['imagem_capa'];
    check($created['status'] === 'publicado' && $created['publicado_em'] !== null && $created['slug'] !== '', 'Criação grava slug, situação e data de publicação');
    $public = request('/public/conteudo.php?slug=' . $created['slug']);
    check($public['status'] === 200 && bodyContains($public, '&lt;script&gt;alert(1)&lt;/script&gt;') && !bodyContains($public, '<script>alert(1)</script>'), 'Página de leitura escapa HTML e scripts');
    check(bodyContains($public, '/app/views/admin/uploads/'), 'Capa usa endereço válido na página pública');
    check(request('/app/views/admin/' . $created['imagem_capa'])['status'] === 200, 'Imagem enviada é servida corretamente');
    check(bodyContains(request($adminPath . 'conteudo_listar.php?' . http_build_query(['busca'=>'Teste de integração','status'=>'publicado']), null, 'admin'), '&lt;script&gt;'), 'Busca e filtro da listagem encontram a publicação e escapam HTML');
    $editPath = $adminPath . 'conteudos_editar.php?id=' . $id;
    $csrfAdmin = token($editPath, 'admin');
    $payload['csrf'] = $csrfAdmin;
    $payload['titulo'] = 'Publicação editada pelo teste';
    $payload['corpo'] = 'Corpo revisado.';
    $r = request($editPath, $payload, 'admin');
    check($r['status'] === 302, 'Edição salva os campos reais do schema');
    $after = $pdo->query('SELECT * FROM conteudos WHERE id=' . $id)->fetch();
    check($after['titulo'] === $payload['titulo'] && $after['corpo'] === $payload['corpo'] && $after['imagem_capa'] === $created['imagem_capa'] && $after['slug'] === $created['slug'], 'Edição preserva capa e slug sem novo upload');
    $r = request($editPath, array_merge($payload, ['imagem'=>new CURLFile($image, 'image/png', 'new.png')]), 'admin', true);
    $after = $pdo->query('SELECT * FROM conteudos WHERE id=' . $id)->fetch();
    $files[] = $root . '/app/views/admin/' . $after['imagem_capa'];
    check($r['status'] === 302 && $after['imagem_capa'] !== $created['imagem_capa'] && !is_file($root . '/app/views/admin/' . $created['imagem_capa']), 'Substituição grava nova capa e remove a antiga após salvar');
    $r = request($editPath, array_merge($payload, ['status'=>'rascunho']), 'admin');
    check($r['status'] === 302 && request('/public/conteudo.php?slug=' . $created['slug'])['status'] === 404, 'Retirar publicação do ar oculta a página pública');
    $studentToken = token($newPath, 'student');
    $studentPayload = array_merge($payload, ['csrf'=>$studentToken,'titulo'=>'Rascunho do aluno','status'=>'publicado']);
    check(bodyContains(request($newPath, $studentPayload, 'student'), 'situação permitida'), 'Aluno não publica diretamente');
    $studentPayload['status'] = 'revisao';
    check(request($newPath, $studentPayload, 'student')['status'] === 302, 'Aluno envia conteúdo para revisão');
    $studentContent = $pdo->query("SELECT * FROM conteudos WHERE titulo='Rascunho do aluno'")->fetch();
    check(!bodyContains(request($adminPath . 'conteudo_listar.php', null, 'student'), 'Publicação editada pelo teste'), 'Aluno vê somente suas publicações na gestão');
    check(request($editPath, null, 'student')['status'] === 302 && request($adminPath . 'conteudos_excluir.php?id=' . $id, ['csrf'=>$studentToken], 'student')['status'] === 302, 'Aluno não edita nem exclui conteúdo de outro autor');
    $reviewPath = $adminPath . 'conteudos_editar.php?id=' . $studentContent['id'];
    $review = array_merge($studentPayload, ['csrf'=>token($reviewPath, 'editor'),'status'=>'publicado']);
    check(request($reviewPath, $review, 'editor')['status'] === 302 && request('/public/conteudo.php?slug=' . $studentContent['slug'])['status'] === 200, 'Editor publica conteúdo enviado por aluno');
    check(request($reviewPath, null, 'student')['status'] === 302, 'Aluno não altera conteúdo já publicado');
    check(request($adminPath . 'conteudos_editar.php?id=999999', null, 'admin')['status'] === 302, 'Editor trata identificador inexistente');
    $deletePath = $adminPath . 'conteudos_excluir.php?id=' . $id;
    check(bodyContains(request($deletePath, null, 'admin'), 'Deseja excluir este conteúdo?') && (int) $pdo->query('SELECT COUNT(*) FROM conteudos WHERE id=' . $id)->fetchColumn() === 1, 'GET de exclusão apenas exibe confirmação');
    check(request($deletePath, ['csrf'=>'invalido'], 'admin')['status'] === 403 && (int) $pdo->query('SELECT COUNT(*) FROM conteudos WHERE id=' . $id)->fetchColumn() === 1, 'Exclusão sem CSRF válido não remove conteúdo');
    check(request($deletePath, ['csrf'=>token($deletePath, 'admin')], 'admin')['status'] === 302 && (int) $pdo->query('SELECT COUNT(*) FROM conteudos WHERE id=' . $id)->fetchColumn() === 0 && !is_file($root . '/app/views/admin/' . $after['imagem_capa']), 'Exclusão confirmada remove registro e capa');
    $logoutToken = token($adminPath . 'index.php', 'student');
    check(request($adminPath . 'logout.php', ['csrf'=>$logoutToken], 'student')['status'] === 302 && request($adminPath . 'index.php', null, 'student')['location'] === 'login.php', 'Logout encerra sessão e bloqueia o painel');
    $pdo->exec("UPDATE usuarios SET status='inativo' WHERE email='aluno.teste@example.test'");
    check(bodyContains(login('inactive', 'aluno.teste@example.test', 'TesteSeguro@2026'), 'E-mail ou senha inválidos'), 'Usuário inativo não autentica');
    check(!preg_match('/(?:<b>)?(?:Warning|Fatal error|Deprecated|Parse error)(?:<\/b>)?:/i', implode("\n", $httpBodies)), 'Fluxos HTTP não emitem avisos ou erros PHP');
    check(request('/public/assets/css/styles.css')['status'] === 200 && request('/public/assets/css/auth.css')['status'] === 200 && request('/app/views/admin/css/admin.css')['status'] === 200, 'Todas as folhas de estilo principais respondem');
    // Conferência dos links locais renderizados no portal.
    $dom = new DOMDocument();
    @$dom->loadHTML($home['body']);
    $links = [];
    foreach ($dom->getElementsByTagName('a') as $a) {
        $href = $a->getAttribute('href');
        if (str_starts_with($href, '/') && !isset($links[$href])) {
            $links[$href] = true;
            $response = request($href);
            check($response['status'] < 400, 'Link público válido: ' . $href);
        }
    }
    echo "\nRESULTADO: $checks verificações, $failures falhas.\n";
    if (in_array('--visual', $argv, true) && $failures === 0) {
        if (is_file($runtime . '/stop')) unlink($runtime . '/stop');
        file_put_contents($runtime . '/ready.json', json_encode(['schema'=>$schema,'url'=>$base,'checks'=>$checks], JSON_PRETTY_PRINT));
        echo "Servidor visual pronto: $base — conta de demonstração do seed.\n";
        $until = time() + 1800;
        while (!is_file($runtime . '/stop') && time() < $until) usleep(250000);
    }
} catch (Throwable $exception) {
    $failures++;
    fwrite(STDERR, "ERRO: " . $exception->getMessage() . "\n");
} finally {
    if (is_resource($server)) { proc_terminate($server); proc_close($server); }
    // Arquivos desta execução, nunca uploads preexistentes.
    foreach (array_unique(array_merge($files, array_values($cookieFiles))) as $file) {
        if (is_file($file)) unlink($file);
    }
    if (preg_match('/^portal_si_test_[a-f0-9]{12}$/', $schema)) $pdo->exec('DROP SCHEMA IF EXISTS "' . $schema . '" CASCADE');
    foreach (['ready.json','stop'] as $file) if (is_file($runtime . '/' . $file)) unlink($runtime . '/' . $file);
    echo "Schema temporário removido; dados originais preservados.\n";
}
exit($failures ? 1 : 0);
