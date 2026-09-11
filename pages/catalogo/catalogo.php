<?php

declare(strict_types=1);

require_once '../../config/conexao.php';

$categoriaSelecionada = max(0, (int) ($_GET['categoria'] ?? 0));

$categorias = $pdo->query(
    "SELECT
        c.id_categoria,
        c.nome_categoria,
        COUNT(DISTINCT pc.id_produto) AS total_produtos
    FROM categoria AS c
    LEFT JOIN produto_categoria AS pc
        ON pc.id_categoria = c.id_categoria
    GROUP BY c.id_categoria, c.nome_categoria
    ORDER BY c.nome_categoria"
)->fetchAll();

$sql = "SELECT
    p.id_produto,
    p.nome_produto,
    p.descricao,
    p.imagem,
    p.quantidade_estoque,
    p.valor_unitario,
    COALESCE(
        GROUP_CONCAT(DISTINCT c.nome_categoria ORDER BY c.nome_categoria SEPARATOR ', '),
        'Sem categoria'
    ) AS categorias
FROM produto AS p
LEFT JOIN produto_categoria AS pc
    ON pc.id_produto = p.id_produto
LEFT JOIN categoria AS c
    ON c.id_categoria = pc.id_categoria
WHERE :categoria = 0
    OR EXISTS (
        SELECT 1
        FROM produto_categoria AS filtro
        WHERE filtro.id_produto = p.id_produto
            AND filtro.id_categoria = :categoria_filtro
    )
GROUP BY
    p.id_produto,
    p.nome_produto,
    p.descricao,
    p.imagem,
    p.quantidade_estoque,
    p.valor_unitario
ORDER BY p.nome_produto";

$consulta = $pdo->prepare($sql);
$consulta->execute([
    'categoria' => $categoriaSelecionada,
    'categoria_filtro' => $categoriaSelecionada
]);
$produtos = $consulta->fetchAll();

$escapar = static fn(mixed $valor): string => htmlspecialchars(
    (string) $valor,
    ENT_QUOTES,
    'UTF-8'
);

$formatarMoeda = static fn(float $valor): string => 'R$ ' . number_format(
    $valor,
    2,
    ',',
    '.'
);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">

    <link rel="icon" href="../../assets/img/icone.png">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Catálogo - Papiro's</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../../assets/css/catalogo.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

    <?php include '../../includes/header.php'; ?>

    <section class="catalogo">

        <div class="container">

            <h1>Catálogo</h1>

            <form class="row g-2 mb-4" method="GET">

                <div class="col-auto">
                    <select name="categoria" class="form-select">

                        <option value="">Todas as categorias</option>

                        <?php foreach ($categorias as $categoria) { ?>

                            <option value="<?= (int) $categoria['id_categoria'] ?>" <?= $categoriaSelecionada === (int) $categoria['id_categoria'] ? 'selected' : '' ?>>
                                <?= $escapar($categoria['nome_categoria']) ?>
                                (<?= (int) $categoria['total_produtos'] ?>)
                            </option>

                        <?php } ?>

                    </select>
                </div>

                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>

            </form>

            <div class="row">

                <?php
                if ($produtos === []) {

                    echo "<p>Nenhum produto cadastrado.</p>";

                } else {

                    foreach ($produtos as $produto) {
                        ?>

                        <div class="col-12 col-md-6 col-lg-4 mb-4">

                            <div class="catalogo-card">

                                <div class="catalogo-img">
                                    <img src="../../assets/img/<?= $escapar($produto['imagem']) ?>"
                                        alt="<?= $escapar($produto['nome_produto']) ?>">
                                </div>

                                <div class="catalogo-info">

                                    <h3><?= $escapar($produto['nome_produto']) ?></h3>

                                    <p><?= $escapar($produto['descricao']) ?></p>
                                    <p class="text-secondary mb-1">
                                        <?= $escapar($produto['categorias']) ?>
                                    </p>
                                    <p class="fw-bold mb-0">
                                        <?= $formatarMoeda((float) $produto['valor_unitario']) ?>
                                    </p>

                                </div>

                            </div>

                        </div>

                        <?php
                    }
                }
                ?>

            </div>

        </div>

    </section>

    <?php include("../../includes/footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>