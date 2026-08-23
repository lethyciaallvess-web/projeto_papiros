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

    <?php
    include("../../config/conexao.php");
    include("../../includes/funcao.php");
    include("../../includes/header.php");
    ?>

    <section class="catalogo">

        <div class="container">

            <h1>Catálogo</h1>

            <?php

            $sql = "SELECT * FROM produto ORDER BY nome_produto";

            $resultado = mysqli_query($conexao, $sql);

            $produtos = mysqli_fetch_all($resultado, MYSQLI_ASSOC);

            $categorias = ["Cadeira", "Mesa", "Armário", "Longarina", "Mocho"];

            $categoriaSelecionada = $_GET['categoria'] ?? '';

            ?>

            <form class="row g-2 mb-4" method="GET">

                <div class="col-auto">
                    <select name="categoria" class="form-select">

                        <option value="">Todas as categorias</option>

                        <?php foreach ($categorias as $categoria) { ?>

                            <option value="<?= $categoria ?>"
                                <?= $categoriaSelecionada === $categoria ? 'selected' : '' ?>>
                                <?= $categoria ?> (<?= contarProdutosPorCategoria($produtos, $categoria) ?>)
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
                if (!validarProdutos($produtos)) {

                    echo "<p>Nenhum produto cadastrado.</p>";

                } else {

                    $produtosExibidos = $categoriaSelecionada !== ''
                        ? filtrarProdutosPorCategoria($produtos, $categoriaSelecionada)
                        : $produtos;

                    if (count($produtosExibidos) > 0) {

                        foreach ($produtosExibidos as $produto) {
                ?>

                        <div class="col-12 col-md-6 col-lg-4 mb-4">

                            <div class="catalogo-card">

                                <div class="catalogo-img">
                                    <img src="../../assets/img/<?= $produto['imagem']; ?>"
                                        alt="<?= $produto['nome_produto']; ?>">
                                </div>

                                <div class="catalogo-info">

                                    <h3><?= $produto['nome_produto']; ?></h3>

                                    <p><?= $produto['descricao']; ?></p>

                                </div>

                            </div>

                        </div>

                <?php
                        }
                    } else {

                        echo "<p>Nenhum produto encontrado.</p>";
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