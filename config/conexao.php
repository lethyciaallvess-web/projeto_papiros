<?php

declare(strict_types=1);

$servidor = getenv('PAPIROS_DB_HOST') ?: '127.0.0.1';
$porta = getenv('PAPIROS_DB_PORT') ?: '3306';
$usuario = getenv('PAPIROS_DB_USER') ?: 'root';
$senha = getenv('PAPIROS_DB_PASSWORD') ?: '';
$banco = getenv('PAPIROS_DB_NAME') ?: 'projeto_papiros';

$pdo = new PDO(
    "mysql:host={$servidor};port={$porta};dbname={$banco};charset=utf8mb4",
    $usuario,
    $senha,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]
);
