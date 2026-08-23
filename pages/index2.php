<?php
session_start();

if (!isset($_SESSION['projeto_papiros'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin - Papiro's</title>

    <link rel="icon" href="../assets/img/icone.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="bg-light">

    <?php include __DIR__ . '/../includes/header2.php'; ?>

    <main class="container py-4">

        <div class="mb-4">
            <h1 class="h2 fw-bold">Dashboard de Estoque</h1>

            <p class="text-secondary">
                Acompanhamento geral dos produtos e categorias.
            </p>
        </div>

        <!-- mensagem de carregar, erro ou sem  dados -->
        <div id="mensagem-dashboard" class="alert alert-info" role="alert">
            <span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>

            Carregando dados da dashboard...
        </div>

        <!-- card dos indicador -->
        <section id="indicadores-dashboard" class="row g-4 mb-5" aria-label="Indicadores de estoque">
            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <p class="text-secondary mb-2">
                            Total de produtos
                        </p>

                        <h2 id="total-produtos" class="display-6 fw-bold mb-0">
                            0
                        </h2>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <p class="text-secondary mb-2">
                            Unidades em estoque
                        </p>

                        <h2 id="estoque-total" class="display-6 fw-bold text-primary mb-0">
                            0
                        </h2>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <p class="text-secondary mb-2">
                            Estoque crítico
                        </p>

                        <h2 id="total-criticos" class="display-6 fw-bold text-warning mb-0">
                            0
                        </h2>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <p class="text-secondary mb-2">
                            Sem estoque
                        </p>

                        <h2 id="total-sem-estoque" class="display-6 fw-bold text-danger mb-0">
                            0
                        </h2>
                    </div>
                </div>
            </div>
        </section>

        <!-- indicador por categoria -->
        <section class="card shadow-sm border-0 mb-5">
            <div class="card-header bg-white py-3">
                <h2 class="h4 mb-0">
                    Estoque por categoria
                </h2>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Categoria</th>
                            <th>Produtos</th>
                            <th>Estoque total</th>
                            <th>Média</th>
                            <th>Sem estoque</th>
                            <th>Críticos</th>
                            <th>Normais</th>
                        </tr>
                    </thead>

                    <tbody id="tabela-categorias">
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                Carregando categorias...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- prod que precisam de reposicao -->
        <section class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h2 class="h4 mb-0">
                    Produtos que precisam de reposição
                </h2>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Produto</th>
                            <th>Categoria</th>
                            <th>Quantidade</th>
                            <th>Situação</th>
                        </tr>
                    </thead>

                    <tbody id="tabela-reposicao">
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                Carregando produtos...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../dist/main.js"></script>

</body>

</html>