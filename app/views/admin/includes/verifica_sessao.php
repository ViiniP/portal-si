<?php
require_once __DIR__ . '/../../../helpers/auth.php';
exigirLogin('login.php');
if (!podeAcessarPainel()) {
    header('Location: ' . portalUrl('public/index.php'));
    exit;
}
