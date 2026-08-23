<?php
session_start();

if (!isset($_SESSION['projeto_papiros'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../../config/conexao.php';

$id = $_GET['editar'] ?? '';
$produtoEditar = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $acao = $_POST['acao'];
    $idProduto = $_POST['id_produto'] ?? '';
    $nome = trim($_POST['nome_produto'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $imagem = trim($_POST['imagem'] ?? '');

    if ($acao === 'salvar') {

        if ($idProduto) {
            $stmt = mysqli_prepare($conexao,
                "UPDATE produto SET nome_produto=?, descricao=?, imagem=? WHERE id_produto=?"
            );

            mysqli_stmt_bind_param($stmt, "sssi",
                $nome, $descricao, $imagem, $idProduto
            );

        } else {

            $stmt = mysqli_prepare($conexao,
                "INSERT INTO produto (nome_produto, descricao, imagem) VALUES (?, ?, ?)"
            );

            mysqli_stmt_bind_param($stmt, "sss",
                $nome, $descricao, $imagem
            );
        }

        mysqli_stmt_execute($stmt);
        header('Location: catalogo2.php?msg=salvo');
        exit;
    }

    if ($acao === 'excluir') {

        $stmt = mysqli_prepare($conexao,
            "DELETE FROM produto WHERE id_produto=?"
        );

        mysqli_stmt_bind_param($stmt, "i", $idProduto);
        mysqli_stmt_execute($stmt);

        header('Location: catalogo2.php?msg=excluido');
        exit;
    }
}

if ($id) {
    $stmt = mysqli_prepare($conexao,
        "SELECT * FROM produto WHERE id_produto=?"
    );

    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $produtoEditar = mysqli_fetch_assoc(
        mysqli_stmt_get_result($stmt)
    );
}

$resultado = mysqli_query(
    $conexao,
    "SELECT * FROM produto ORDER BY nome_produto"
);

$produtos = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Catálogo Admin - Papiro's</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

<?php include '../../includes/header2.php'; ?>

<main class="container py-5">

    <h1 class="mb-4">Catálogo</h1>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success">
            <?= $_GET['msg'] === 'excluido'
                ? 'Produto excluído com sucesso!'
                : 'Produto salvo com sucesso!' ?>
        </div>
    <?php endif; ?>

    <form method="post" class="mb-5">

        <input type="hidden" name="acao" value="salvar">
        <input type="hidden" name="id_produto"
               value="<?= $produtoEditar['id_produto'] ?? '' ?>">

        <div class="mb-3">
            <label class="form-label">Produto</label>

            <input
                type="text"
                name="nome_produto"
                class="form-control"
                value="<?= $produtoEditar['nome_produto'] ?? '' ?>"
                required
            >
        </div>

        <div class="mb-3">
            <label class="form-label">Descrição</label>

            <textarea
                name="descricao"
                class="form-control"
                required
            ><?= $produtoEditar['descricao'] ?? '' ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Imagem</label>

            <input
                type="text"
                name="imagem"
                class="form-control"
                placeholder="exemplo.png"
                value="<?= $produtoEditar['imagem'] ?? '' ?>"
                required
            >
        </div>

        <button class="btn btn-success">
            <?= $produtoEditar ? 'Salvar alterações' : 'Adicionar produto' ?>
        </button>

        <?php if ($produtoEditar): ?>
            <a href="catalogo2.php" class="btn btn-secondary">
                Cancelar
            </a>
        <?php endif; ?>

    </form>


    <table class="table table-striped align-middle">

        <thead>
            <tr>
                <th>Imagem</th>
                <th>Produto</th>
                <th>Descrição</th>
                <th>Ações</th>
            </tr>
        </thead>

        <tbody>

        <?php foreach ($produtos as $produto): ?>

            <tr>

                <td>
                    <img
                        src="../../assets/img/<?= $produto['imagem'] ?>"
                        width="70"
                        alt="<?= $produto['nome_produto'] ?>"
                    >
                </td>

                <td><?= $produto['nome_produto'] ?></td>

                <td><?= $produto['descricao'] ?></td>

                <td>

                    <a
                        href="catalogo2.php?editar=<?= $produto['id_produto'] ?>"
                        class="btn btn-warning btn-sm"
                    >
                        Editar
                    </a>

                    <form method="post"
                          class="d-inline"
                          onsubmit="return confirm('Deseja excluir este produto?')">

                        <input type="hidden" name="acao" value="excluir">

                        <input
                            type="hidden"
                            name="id_produto"
                            value="<?= $produto['id_produto'] ?>"
                        >

                        <button class="btn btn-danger btn-sm">
                            Excluir
                        </button>

                    </form>

                </td>

            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>