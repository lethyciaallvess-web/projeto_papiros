<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

$busca = trim($_GET['busca'] ?? '');
$categoria = max(0, (int) ($_GET['categoria'] ?? 0));
$pagina = max(1, (int) ($_GET['pagina'] ?? 1));
$limite = min(50, max(1, (int) ($_GET['limite'] ?? 10)));

try {
    require_once __DIR__ . '/../config/conexao.php';

    $consulta = $pdo->query(
        'call sp_dashboard_indicadores()'
    );
    $categorias = $consulta->fetchAll(PDO::FETCH_ASSOC);
    $consulta->closeCursor();

    $consulta = $pdo->query(
        'call sp_dashboard_inventario()'
    );
    $inventario = $consulta->fetchAll(PDO::FETCH_ASSOC);
    $consulta->closeCursor();

    $consulta = $pdo->prepare(
        'call sp_contar_produtos(?, ?)'
    );
    $consulta->execute([$busca, $categoria]);
    $contagem = $consulta->fetch(PDO::FETCH_ASSOC);
    $consulta->closeCursor();

    $totalRegistros = (int) (
        $contagem['total_registros'] ?? 0
    );

    $totalPaginas = (int) ceil(
        $totalRegistros / $limite
    );

    if ($totalPaginas > 0 && $pagina > $totalPaginas) {
        $pagina = $totalPaginas;
    }

    $offset = ($pagina - 1) * $limite;

    $consulta = $pdo->prepare(
        'call sp_listar_produtos(?, ?, ?, ?)'
    );
    $consulta->execute([
        $busca,
        $categoria,
        $limite,
        $offset
    ]);
    $produtos = $consulta->fetchAll(PDO::FETCH_ASSOC);
    $consulta->closeCursor();

    $categorias = array_map(
        static fn(array $item): array => [
            'id_categoria' =>
                (int) $item['id_categoria'],

            'nome_categoria' =>
                (string) $item['nome_categoria'],

            'total_produtos' =>
                (int) $item['total_produtos'],

            'estoque_total' =>
                (int) $item['estoque_total'],

            'media_estoque' =>
                (float) $item['media_estoque'],

            'valor_total_estoque' =>
                (float) $item['valor_total_estoque'],

            'produtos_sem_estoque' =>
                (int) $item['produtos_sem_estoque'],

            'produtos_estoque_critico' =>
                (int) $item['produtos_estoque_critico'],

            'produtos_estoque_normal' =>
                (int) $item['produtos_estoque_normal']
        ],
        $categorias
    );

    $inventario = array_map(
        static fn(array $item): array => [
            'id_produto' =>
                (int) $item['id_produto'],

            'quantidade_estoque' =>
                (int) $item['quantidade_estoque'],

            'valor_unitario' =>
                (float) $item['valor_unitario'],

            'valor_total' =>
                (float) $item['valor_total'],

            'status_estoque' =>
                (string) $item['status_estoque']
        ],
        $inventario
    );

    $produtos = array_map(
        static fn(array $item): array => [
            'id_produto' =>
                (int) $item['id_produto'],

            'nome_produto' =>
                (string) $item['nome_produto'],

            'quantidade_estoque' =>
                (int) $item['quantidade_estoque'],

            'valor_unitario' =>
                (float) $item['valor_unitario'],

            'valor_total' =>
                (float) $item['valor_total'],

            'status_estoque' =>
                (string) $item['status_estoque'],

            'categorias' =>
                (string) $item['nome_categoria']
        ],
        $produtos
    );

    echo json_encode(
        [
            'sucesso' => true,
            'pagina' => $pagina,
            'limite' => $limite,
            'total_registros' => $totalRegistros,
            'total_paginas' => $totalPaginas,
            'categorias' => $categorias,
            'inventario' => $inventario,
            'produtos' => $produtos
        ],
        JSON_UNESCAPED_UNICODE |
        JSON_THROW_ON_ERROR
    );
} catch (Throwable $erro) {
    error_log($erro->getMessage());

    http_response_code(500);

    echo json_encode(
        [
            'sucesso' => false,
            'mensagem' => 'erro ao carregar a dashboard.'
        ],
        JSON_UNESCAPED_UNICODE
    );
}