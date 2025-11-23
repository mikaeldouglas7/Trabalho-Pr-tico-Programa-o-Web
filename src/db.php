<?php
// sprint_manager/src/db.php

$DB_NAME = "sprint_manager";
$DB_HOST = "127.0.0.1"; // Mais confiável que 'localhost'
$DB_PORT = "8889";       // A porta do seu screenshot do MAMP!
$DB_USER = "root";
$DB_PASS = "root";

try {
    // String de conexão (DSN) ATUALIZADA para usar Host e Porta
    $pdo = new PDO(
        "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    // Mensagem de erro mais clara
    die("Erro na conexão com o banco de dados (host/port): " . $e->getMessage());
}