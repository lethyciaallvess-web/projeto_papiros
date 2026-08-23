<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

try {
    require_once __DIR__ . '/../config/conexao.php';

    $sqlCategorias = "
        SELECT
            id_categoria,
            nome_categoria,
            total_produtos,
            estoque_total,
            media_estoque,
            produtos_sem_estoque,
            produtos_estoque_critico,
            produtos_estoque_normal
        FROM vw_estoque_por_categoria
        ORDER BY nome_categoria
    ";

    $consultaCategorias = $pdo->prepare($sqlCategorias);
    $consultaCategorias->execute();

    $categorias = $consultaCategorias->fetchAll(PDO::FETCH_ASSOC);

    $sqlProdutos = "
        SELECT
            id_produto,
            nome_produto,
            quantidade_estoque,
            status_estoque,
            categorias
        FROM vw_produtos_estoque
        ORDER BY nome_produto
    ";

    $consultaProdutos = $pdo->prepare($sqlProdutos);
    $consultaProdutos->execute();

    $produtos = $consultaProdutos->fetchAll(PDO::FETCH_ASSOC);

    $categoriasFormatadas = array_map(
        static function (array $categoria): array {
            return [
                'id_categoria' => (int) $categoria['id_categoria'],
                'nome_categoria' => $categoria['nome_categoria'],
                'total_produtos' => (int) $categoria['total_produtos'],
                'estoque_total' => (int) $categoria['estoque_total'],
                'media_estoque' => (float) $categoria['media_estoque'],
                'produtos_sem_estoque' =>
                    (int) $categoria['produtos_sem_estoque'],
                'produtos_estoque_critico' =>
                    (int) $categoria['produtos_estoque_critico'],
                'produtos_estoque_normal' =>
                    (int) $categoria['produtos_estoque_normal']
            ];
        },
        $categorias
    );

    $produtosFormatados = array_map(
        static function (array $produto): array {
            return [
                'id_produto' => (int) $produto['id_produto'],
                'nome_produto' => $produto['nome_produto'],
                'quantidade_estoque' =>
                    (int) $produto['quantidade_estoque'],
                'status_estoque' => $produto['status_estoque'],
                'categorias' => $produto['categorias']
            ];
        },
        $produtos
    );

    echo json_encode(
        [
            'sucesso' => true,
            'categorias' => $categoriasFormatadas,
            'produtos' => $produtosFormatados
        ],
        JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
    );
} catch (Throwable $erro) {
    http_response_code(500);

    echo json_encode(
        [
            'sucesso' => false,
            'mensagem' => 'Não foi possível carregar os dados da dashboard.'
        ],
        JSON_UNESCAPED_UNICODE
    );
}