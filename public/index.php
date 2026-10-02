<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers/seguranca.php';
require_once __DIR__ . '/../app/models/Conteudo.php';

$conteudo = new Conteudo($pdo);
$conteudos_recentes = $conteudo->listarPublicados();

require __DIR__ . '/../app/views/home/index.php';

?>