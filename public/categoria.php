<?php
require_once __DIR__ . '/../app/helpers/portal.php';
$_GET['categoria'] = textoEntrada($_GET, 'slug');
if ($_GET['categoria'] === '') {
    header('Location: index.php');
    exit;
}
require __DIR__ . '/../app/views/home/index.php';
