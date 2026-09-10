<?php

declare(strict_types=1);

require_once '../../includes/autenticacao.php';

$usuarioLogado = exigirAutenticacao('../login.php');
$csrfToken = tokenCsrf();

require_once '../../config/conexao.php';

$redirecionar = static function (string $mensagem): never {
    header("Location: categoria.php?msg={$mensagem}");
    exit;
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!tokenCsrfValido($_POST['csrf_token'] ?? null)) {
        $redirecionar('csrf');
    }

    $acao = (string) ($_POST['acao'] ?? '');
    $idCategoria = max(0, (int) ($_POST['id_categoria'] ?? 0));

    try {
        if ($acao === 'salvar') {
            $nomeCategoria = trim(
                (string) ($_POST['nome_categoria'] ?? '')
            );

            if ($nomeCategoria === '' || mb_strlen($nomeCategoria) > 100) {
                $redirecionar('invalida');
            }

            $verificarNome = $pdo->prepare(
                'SELECT COUNT(*)
                FROM categoria
                WHERE nome_categoria = ?
                    AND id_categoria <> ?'
            );
            $verificarNome->execute([$nomeCategoria, $idCategoria]);

            if ((int) $verificarNome->fetchColumn() > 0) {
                $redirecionar('duplicada');
            }

            if ($idCategoria > 0) {
                $consulta = $pdo->prepare(
                    'UPDATE categoria
                    SET nome_categoria = ?
                    WHERE id_categoria = ?'
                );
                $consulta->execute([$nomeCategoria, $idCategoria]);

                if ($consulta->rowCount() === 0) {
                    $verificarId = $pdo->prepare(
                        'SELECT COUNT(*)
                        FROM categoria
                        WHERE id_categoria = ?'
                    );
                    $verificarId->execute([$idCategoria]);

                    if ((int) $verificarId->fetchColumn() === 0) {
                        $redirecionar('nao_encontrada');
                    }
                }
            } else {
                $consulta = $pdo->prepare(
                    'INSERT INTO categoria (nome_categoria)
                    VALUES (?)'
                );
                $consulta->execute([$nomeCategoria]);
            }

            $redirecionar('salva');
        }

        if ($acao === 'excluir' && $idCategoria > 0) {
            $contarProdutos = $pdo->prepare(
                'SELECT COUNT(*)
                FROM produto_categoria
                WHERE id_categoria = ?'
            );
            $contarProdutos->execute([$idCategoria]);

            if ((int) $contarProdutos->fetchColumn() > 0) {
                $redirecionar('em_uso');
            }

            $excluir = $pdo->prepare(
                'DELETE FROM categoria WHERE id_categoria = ?'
            );
            $excluir->execute([$idCategoria]);

            if ($excluir->rowCount() === 0) {
                $redirecionar('nao_encontrada');
            }

            $redirecionar('excluida');
        }

        $redirecionar('invalida');
    } catch (Throwable $erro) {
        error_log($erro->getMessage());
        $redirecionar('erro');
    }
}

$idEditar = max(0, (int) ($_GET['editar'] ?? 0));
$categoriaEditar = null;

if ($idEditar > 0) {
    $consulta = $pdo->prepare(
        'SELECT id_categoria, nome_categoria
        FROM categoria
        WHERE id_categoria = ?'
    );
    $consulta->execute([$idEditar]);
    $categoriaEditar = $consulta->fetch() ?: null;
}

$categorias = $pdo->query(
    'SELECT
        c.id_categoria,
        c.nome_categoria,
        COUNT(DISTINCT pc.id_produto) AS produtos_vinculados
    FROM categoria AS c
    LEFT JOIN produto_categoria AS pc
        ON pc.id_categoria = c.id_categoria
    GROUP BY c.id_categoria, c.nome_categoria
    ORDER BY c.nome_categoria'
)->fetchAll();

$mensagens = [
    'salva' => ['success', 'Categoria salva com sucesso.'],
    'excluida' => ['success', 'Categoria excluída com sucesso.'],
    'em_uso' => ['warning', 'A categoria não pode ser excluída porque possui produtos vinculados.'],
    'duplicada' => ['warning', 'Já existe uma categoria com esse nome.'],
    'invalida' => ['warning', 'Informe um nome de categoria válido.'],
    'nao_encontrada' => ['warning', 'A categoria solicitada não foi encontrada.'],
    'csrf' => ['danger', 'A sessão do formulário expirou. Atualize a página e tente novamente.'],
    'erro' => ['danger', 'Não foi possível concluir a operação. Tente novamente.']
];
$mensagemAtual = $mensagens[$_GET['msg'] ?? ''] ?? null;

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
    <link rel="icon" href="../../assets/img/icone.png">
    <title>Categorias - Papiro's</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>
    <?php include '../../includes/header2.php'; ?>

    <main class="container py-5">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h1 class="mb-1">Categorias</h1>
                <p class="text-secondary mb-0">
                    Cadastro, consulta, edição e exclusão das categorias.
                </p>
            </div>

            <a href="catalogo2.php" class="btn btn-outline-primary">
                Voltar aos produtos
            </a>
        </div>

        <?php if ($mensagemAtual): ?>
            <div class="alert alert-<?= $mensagemAtual[0] ?>" role="alert">
                <?= $escapar($mensagemAtual[1]) ?>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-4">
                <section class="card shadow-sm border-0">
                    <div class="card-body w-100">
                        <h2 class="h4 mb-4">
                            <?= $categoriaEditar ? 'Editar categoria' : 'Nova categoria' ?>
                        </h2>

                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?= $escapar($csrfToken) ?>">
                            <input type="hidden" name="acao" value="salvar">
                            <input type="hidden" name="id_categoria"
                                value="<?= (int) ($categoriaEditar['id_categoria'] ?? 0) ?>">

                            <div class="mb-3">
                                <label for="nome-categoria" class="form-label">
                                    Nome da categoria
                                </label>
                                <input type="text" id="nome-categoria" name="nome_categoria"
                                    class="form-control" maxlength="100"
                                    value="<?= $escapar($categoriaEditar['nome_categoria'] ?? '') ?>" required>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success">
                                    <?= $categoriaEditar ? 'Salvar alterações' : 'Cadastrar' ?>
                                </button>

                                <?php if ($categoriaEditar): ?>
                                    <a href="categoria.php" class="btn btn-secondary">Cancelar</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </section>
            </div>

            <div class="col-lg-8">
                <section class="card shadow-sm border-0">
                    <div class="table-responsive w-100">
                        <table class="table table-striped align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Categoria</th>
                                    <th>Produtos vinculados</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($categorias === []): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
                                            Nenhuma categoria cadastrada.
                                        </td>
                                    </tr>
                                <?php endif; ?>

                                <?php foreach ($categorias as $categoria): ?>
                                    <tr>
                                        <td><?= (int) $categoria['id_categoria'] ?></td>
                                        <td><?= $escapar($categoria['nome_categoria']) ?></td>
                                        <td><?= (int) $categoria['produtos_vinculados'] ?></td>
                                        <td class="text-nowrap">
                                            <a href="categoria.php?editar=<?= (int) $categoria['id_categoria'] ?>"
                                                class="btn btn-warning btn-sm">Editar</a>

                                            <form method="post" class="d-inline"
                                                onsubmit="return confirm('Deseja excluir esta categoria?')">
                                                <input type="hidden" name="csrf_token"
                                                    value="<?= $escapar($csrfToken) ?>">
                                                <input type="hidden" name="acao" value="excluir">
                                                <input type="hidden" name="id_categoria"
                                                    value="<?= (int) $categoria['id_categoria'] ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    Excluir
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <?php include '../../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
