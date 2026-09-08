<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

$busca = trim($_GET['busca'] ?? '');
$categoria = max(0, (int) ($_GET['categoria'] ?? 0));
$pagina = max(1, (int) ($_GET['pagina'] ?? 1));
$limite = min(50, max(1, (int) ($_GET['limite'] ?? 10)));
$offset = ($pagina - 1) * $limite;

try {
    require_once __DIR__ . '/../config/conexao.php';

    $categorias = $pdo->query("
        select
            c.id_categoria,
            c.nome_categoria,
            count(distinct p.id_produto) as total_produtos,
            coalesce(sum(p.quantidade_estoque), 0) as estoque_total,
            round(
                coalesce(avg(p.quantidade_estoque), 0),
                2
            ) as media_estoque,
            count(distinct case
                when p.quantidade_estoque = 0
                then p.id_produto
            end) as produtos_sem_estoque,
            count(distinct case
                when p.quantidade_estoque between 1 and 5
                then p.id_produto
            end) as produtos_estoque_critico,
            count(distinct case
                when p.quantidade_estoque > 5
                then p.id_produto
            end) as produtos_estoque_normal
        from categoria as c
        left join produto_categoria as pc
            on pc.id_categoria = c.id_categoria
        left join produto as p
            on p.id_produto = pc.id_produto
        group by
            c.id_categoria,
            c.nome_categoria
        order by
            c.nome_categoria
    ")->fetchAll(PDO::FETCH_ASSOC);

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
        static fn(array $categoria): array => [
            'id_categoria' =>
                (int) $categoria['id_categoria'],

            'nome_categoria' =>
                $categoria['nome_categoria'],

            'total_produtos' =>
                (int) $categoria['total_produtos'],

            'estoque_total' =>
                (int) $categoria['estoque_total'],

            'media_estoque' =>
                (float) $categoria['media_estoque'],

            'produtos_sem_estoque' =>
                (int) $categoria['produtos_sem_estoque'],

            'produtos_estoque_critico' =>
                (int) $categoria['produtos_estoque_critico'],

            'produtos_estoque_normal' =>
                (int) $categoria['produtos_estoque_normal']
        ],
        $categorias
    );

    $produtos = array_map(
        static fn(array $produto): array => [
            'id_produto' =>
                (int) $produto['id_produto'],

            'nome_produto' =>
                $produto['nome_produto'],

            'quantidade_estoque' =>
                (int) $produto['quantidade_estoque'],

            'status_estoque' =>
                $produto['status_estoque'],

            'categorias' =>
                $produto['nome_categoria']
        ],
        $produtos
    );

    echo json_encode(
        [
            'sucesso' => true,
            'pagina' => $pagina,
            'limite' => $limite,
            'categorias' => $categorias,
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
            'mensagem' => 'Erro ao carregar a dashboard.'
        ],
        JSON_UNESCAPED_UNICODE
    );
}