<?php
    $host = 'localhost';
    $dbname = 'portal_si';
    $username = 'postgres';
    $password = 'postgres';
    $port = '5432';
    
    try {
        $pdo = new PDO("pgsql:host=$host;
        port=$port;
        dbname=$dbname",
        $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Conexão com PostgreSQL estabelecida com sucesso!";
    }
    
    catch (PDOException $e){ die("Erro de conexão: " . $e->getMessage()); }
?>