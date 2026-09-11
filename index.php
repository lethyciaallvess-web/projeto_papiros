<?php
require_once 'config/conexao.php';

$categoriasHome = $pdo->query(
    "SELECT
        c.id_categoria,
        c.nome_categoria,
        COUNT(DISTINCT p.id_produto) AS total_produtos,
        MIN(p.imagem) AS imagem
    FROM categoria AS c
    LEFT JOIN produto_categoria AS pc
        ON pc.id_categoria = c.id_categoria
    LEFT JOIN produto AS p
        ON p.id_produto = pc.id_produto
    GROUP BY c.id_categoria, c.nome_categoria
    HAVING COUNT(DISTINCT p.id_produto) > 0
    ORDER BY c.nome_categoria"
)->fetchAll();

$escapar = static fn(mixed $valor): string => htmlspecialchars(
    (string) $valor,
    ENT_QUOTES,
    'UTF-8'
);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papiro's</title>

    <link rel="icon" href="assets/img/icone.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <?php include 'includes/header.php'; ?>

    <section class="banner">
        <div class="container">

            <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">

                <div class="carousel-inner">

                    <div class="carousel-item active">
                        <img src="assets/img/banner.png" class="d-block w-100" alt="Banner">
                    </div>

                    <div class="carousel-item">
                        <img src="assets/img/banner2.png" class="d-block w-100" alt="Banner">
                    </div>

                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>

                <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>

            </div>

            <div class="botoes">
                <a href="pages/catalogo/catalogo.php" class="botao">CATÁLOGO</a>
                <a href="pages/contato/contato.php" class="contato">ENTRE EM CONTATO</a>
            </div>

        </div>
    </section>

    <section class="categorias container text-center py-5">

        <h2 class="mb-5">CATEGORIAS</h2>

        <div class="row justify-content-center g-4">

            <?php foreach ($categoriasHome as $categoria): ?>

                    <div class="col-6 col-md-3">
                        <a href="pages/catalogo/catalogo.php?categoria=<?= (int) $categoria['id_categoria'] ?>"
                            class="text-decoration-none text-dark">

                            <div class="card h-100">
                                <img src="assets/img/<?= $escapar($categoria['imagem']) ?>"
                                    alt="<?= $escapar($categoria['nome_categoria']) ?>">
                                <p><?= $escapar($categoria['nome_categoria']) ?></p>
                                <small class="text-secondary">
                                    <?= (int) $categoria['total_produtos'] ?> produtos
                                </small>
                            </div>

                        </a>
                    </div>

            <?php endforeach; ?>

        </div>

    </section>

    <section class="vantagens py-5">
        <div class="container">
            <div class="row g-4">

                <div class="col-md-6 col-lg-3">
                    <div class="bloco">
                        <img src="assets/img/qualidade.png" alt="Qualidade">
                        <div>
                            <h3>QUALIDADE GARANTIDA</h3>
                            <p>Materiais de alta resistência e durabilidade.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="bloco">
                        <img src="assets/img/configuracao.png" alt="Design">
                        <div>
                            <h3>DESIGN FUNCIONAL</h3>
                            <p>Produtos pensados para o ambiente escolar.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="bloco">
                        <img src="assets/img/fone.png" alt="Atendimento">
                        <div>
                            <h3>ATENDIMENTO ESPECIALIZADO</h3>
                            <p>Soluções personalizadas para sua instituição.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="bloco">
                        <img src="assets/img/confianca.png" alt="Confiança">
                        <div>
                            <h3>CONFIANÇA</h3>
                            <p>Mais de 10 anos de experiência no mercado.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
