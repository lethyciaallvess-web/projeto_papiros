<?php

declare(strict_types=1);

require_once '../../includes/autenticacao.php';

$usuarioLogado = exigirAutenticacao('../login.php');
$csrfToken = tokenCsrf();

require_once '../../config/conexao.php';

$redirecionar = static function (string $mensagem): never {
    header("Location: catalogo2.php?msg={$mensagem}");
    exit;
};

$diretorioImagens = __DIR__ . '/../../assets/img/produtos';

$receberImagem = static function (?array $arquivo) use ($diretorioImagens): ?string {
    if (!$arquivo || ($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new InvalidArgumentException('imagem');
    }

    $tamanho = (int) ($arquivo['size'] ?? 0);

    if ($tamanho <= 0 || $tamanho > 5 * 1024 * 1024) {
        throw new InvalidArgumentException('imagem');
    }

    $arquivoTemporario = (string) ($arquivo['tmp_name'] ?? '');
    $identificador = new finfo(FILEINFO_MIME_TYPE);
    $tipo = $identificador->file($arquivoTemporario);
    $extensoes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif'
    ];

    if (!is_string($tipo) || !isset($extensoes[$tipo])) {
        throw new InvalidArgumentException('imagem');
    }

    if (
        !is_dir($diretorioImagens)
        && !mkdir($diretorioImagens, 0755, true)
        && !is_dir($diretorioImagens)
    ) {
        throw new RuntimeException('Não foi possível criar a pasta de imagens.');
    }

    $nomeArquivo = 'produto-' . bin2hex(random_bytes(12))
        . '.' . $extensoes[$tipo];
    $destino = $diretorioImagens . DIRECTORY_SEPARATOR . $nomeArquivo;

    if (!move_uploaded_file($arquivoTemporario, $destino)) {
        throw new RuntimeException('Não foi possível salvar a imagem.');
    }

    return 'produtos/' . $nomeArquivo;
};

$removerImagemEnviada = static function (?string $imagem) use ($diretorioImagens): void {
    if (!$imagem || !str_starts_with($imagem, 'produtos/')) {
        return;
    }

    $nomeArquivo = basename($imagem);
    $caminho = $diretorioImagens . DIRECTORY_SEPARATOR . $nomeArquivo;

    if (is_file($caminho)) {
        unlink($caminho);
    }
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!tokenCsrfValido($_POST['csrf_token'] ?? null)) {
        $redirecionar('csrf');
    }

    $acao = (string) ($_POST['acao'] ?? '');
    $idProduto = max(0, (int) ($_POST['id_produto'] ?? 0));

    $imagemNova = null;

    try {
        if ($acao === 'salvar') {
            $nome = trim((string) ($_POST['nome_produto'] ?? ''));
            $descricao = trim((string) ($_POST['descricao'] ?? ''));
            $quantidade = filter_var(
                $_POST['quantidade_estoque'] ?? null,
                FILTER_VALIDATE_INT
            );
            $valorRecebido = str_replace(
                ',',
                '.',
                trim((string) ($_POST['valor_unitario'] ?? ''))
            );
            $idCategoria = max(0, (int) ($_POST['id_categoria'] ?? 0));

            if (
                $nome === ''
                || $descricao === ''
                || $quantidade === false
                || $quantidade < 0
                || !is_numeric($valorRecebido)
                || (float) $valorRecebido < 0
                || $idCategoria === 0
            ) {
                $redirecionar('invalido');
            }

            $consultaCategoria = $pdo->prepare(
                'SELECT COUNT(*) FROM categoria WHERE id_categoria = ?'
            );
            $consultaCategoria->execute([$idCategoria]);

            if ((int) $consultaCategoria->fetchColumn() === 0) {
                $redirecionar('categoria');
            }

            $imagemAtual = '';

            if ($idProduto > 0) {
                $consultaImagem = $pdo->prepare(
                    'SELECT imagem FROM produto WHERE id_produto = ?'
                );
                $consultaImagem->execute([$idProduto]);
                $imagemEncontrada = $consultaImagem->fetchColumn();

                if ($imagemEncontrada === false) {
                    $redirecionar('invalido');
                }

                $imagemAtual = (string) $imagemEncontrada;
            }

            try {
                $imagemNova = $receberImagem(
                    isset($_FILES['imagem_arquivo'])
                        && is_array($_FILES['imagem_arquivo'])
                        ? $_FILES['imagem_arquivo']
                        : null
                );
            } catch (InvalidArgumentException) {
                $redirecionar('imagem');
            }

            $imagem = $imagemNova ?? $imagemAtual;

            if ($imagem === '') {
                $redirecionar('imagem_obrigatoria');
            }

            $valorUnitario = number_format(
                (float) $valorRecebido,
                2,
                '.',
                ''
            );

            $pdo->beginTransaction();

            if ($idProduto > 0) {
                $consulta = $pdo->prepare(
                    'UPDATE produto
                    SET nome_produto = ?,
                        descricao = ?,
                        imagem = ?,
                        quantidade_estoque = ?,
                        valor_unitario = ?
                    WHERE id_produto = ?'
                );
                $consulta->execute([
                    $nome,
                    $descricao,
                    $imagem,
                    $quantidade,
                    $valorUnitario,
                    $idProduto
                ]);
            } else {
                $consulta = $pdo->prepare(
                    'INSERT INTO produto (
                        nome_produto,
                        descricao,
                        imagem,
                        quantidade_estoque,
                        valor_unitario
                    ) VALUES (?, ?, ?, ?, ?)'
                );
                $consulta->execute([
                    $nome,
                    $descricao,
                    $imagem,
                    $quantidade,
                    $valorUnitario
                ]);
                $idProduto = (int) $pdo->lastInsertId();
            }

            $removerVinculos = $pdo->prepare(
                'DELETE FROM produto_categoria WHERE id_produto = ?'
            );
            $removerVinculos->execute([$idProduto]);

            $vincularCategoria = $pdo->prepare(
                'INSERT INTO produto_categoria (id_produto, id_categoria)
                VALUES (?, ?)'
            );
            $vincularCategoria->execute([$idProduto, $idCategoria]);

            $pdo->commit();

            if ($imagemNova !== null && $imagemAtual !== '') {
                $removerImagemEnviada($imagemAtual);
            }

            $redirecionar('salvo');
        }

        if ($acao === 'excluir' && $idProduto > 0) {
            $consultaImagem = $pdo->prepare(
                'SELECT imagem FROM produto WHERE id_produto = ?'
            );
            $consultaImagem->execute([$idProduto]);
            $imagemExcluir = $consultaImagem->fetchColumn();

            if ($imagemExcluir === false) {
                $redirecionar('invalido');
            }

            $pdo->beginTransaction();

            $removerVinculos = $pdo->prepare(
                'DELETE FROM produto_categoria WHERE id_produto = ?'
            );
            $removerVinculos->execute([$idProduto]);

            $excluirProduto = $pdo->prepare(
                'DELETE FROM produto WHERE id_produto = ?'
            );
            $excluirProduto->execute([$idProduto]);

            if ($excluirProduto->rowCount() === 0) {
                throw new RuntimeException('Produto não encontrado.');
            }

            $pdo->commit();
            $removerImagemEnviada((string) $imagemExcluir);
            $redirecionar('excluido');
        }

        $redirecionar('invalido');
    } catch (Throwable $erro) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        if ($imagemNova !== null) {
            $removerImagemEnviada($imagemNova);
        }

        error_log($erro->getMessage());
        $redirecionar('erro');
    }
}

$idEditar = max(0, (int) ($_GET['editar'] ?? 0));
$produtoEditar = null;

if ($idEditar > 0) {
    $consulta = $pdo->prepare(
        'SELECT
            p.*,
            (
                SELECT MIN(pc.id_categoria)
                FROM produto_categoria AS pc
                WHERE pc.id_produto = p.id_produto
            ) AS id_categoria
        FROM produto AS p
        WHERE p.id_produto = ?'
    );
    $consulta->execute([$idEditar]);
    $produtoEditar = $consulta->fetch() ?: null;
}

$categorias = $pdo->query(
    'SELECT id_categoria, nome_categoria
    FROM categoria
    ORDER BY nome_categoria'
)->fetchAll();

$produtos = $pdo->query(
    "SELECT
        p.id_produto,
        p.nome_produto,
        p.descricao,
        p.imagem,
        p.quantidade_estoque,
        p.valor_unitario,
        COALESCE(
            GROUP_CONCAT(
                DISTINCT c.nome_categoria
                ORDER BY c.nome_categoria
                SEPARATOR ', '
            ),
            'Sem categoria'
        ) AS categorias
    FROM produto AS p
    LEFT JOIN produto_categoria AS pc
        ON pc.id_produto = p.id_produto
    LEFT JOIN categoria AS c
        ON c.id_categoria = pc.id_categoria
    GROUP BY
        p.id_produto,
        p.nome_produto,
        p.descricao,
        p.imagem,
        p.quantidade_estoque,
        p.valor_unitario
    ORDER BY p.nome_produto"
)->fetchAll();

$mensagens = [
    'salvo' => ['success', 'Produto salvo e vinculado à categoria com sucesso.'],
    'excluido' => ['success', 'Produto e seus vínculos foram excluídos com sucesso.'],
    'invalido' => ['warning', 'Preencha todos os campos com valores válidos.'],
    'categoria' => ['warning', 'Selecione uma categoria existente.'],
    'imagem' => ['warning', 'Escolha uma imagem JPG, PNG, WEBP ou GIF com até 5 MB.'],
    'imagem_obrigatoria' => ['warning', 'Escolha uma imagem para o novo produto.'],
    'csrf' => ['danger', 'A sessão do formulário expirou. Atualize a página e tente novamente.'],
    'erro' => ['danger', 'Não foi possível concluir a operação. Nenhuma alteração parcial foi mantida.']
];
$mensagemAtual = $mensagens[$_GET['msg'] ?? ''] ?? null;

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../assets/img/icone.png">
    <title>Produtos - Papiro's</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>
    <?php include '../../includes/header2.php'; ?>

    <main class="container py-5">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h1 class="mb-1">Produtos</h1>
                <p class="text-secondary mb-0">
                    Cadastro, consulta, edição e exclusão do catálogo.
                </p>
            </div>

            <a href="categoria.php" class="btn btn-outline-primary">
                Gerenciar categorias
            </a>
        </div>

        <?php if ($mensagemAtual): ?>
            <div class="alert alert-<?= $mensagemAtual[0] ?>" role="alert">
                <?= $escapar($mensagemAtual[1]) ?>
            </div>
        <?php endif; ?>

        <section class="card shadow-sm border-0 mb-5">
            <div class="card-body w-100">
                <h2 class="h4 mb-4">
                    <?= $produtoEditar ? 'Editar produto' : 'Adicionar produto' ?>
                </h2>

                <form method="post" enctype="multipart/form-data" class="row g-3">
                    <input type="hidden" name="csrf_token" value="<?= $escapar($csrfToken) ?>">
                    <input type="hidden" name="acao" value="salvar">
                    <input type="hidden" name="id_produto"
                        value="<?= (int) ($produtoEditar['id_produto'] ?? 0) ?>">

                    <div class="col-md-6">
                        <label for="nome-produto" class="form-label">Produto</label>
                        <input type="text" id="nome-produto" name="nome_produto" class="form-control"
                            maxlength="100" value="<?= $escapar($produtoEditar['nome_produto'] ?? '') ?>" required>
                    </div>

                    <div class="col-md-3">
                        <label for="quantidade-estoque" class="form-label">Quantidade em estoque</label>
                        <input type="number" id="quantidade-estoque" name="quantidade_estoque"
                            class="form-control" min="0" step="1"
                            value="<?= (int) ($produtoEditar['quantidade_estoque'] ?? 0) ?>" required>
                    </div>

                    <div class="col-md-3">
                        <label for="valor-unitario" class="form-label">Valor unitário (R$)</label>
                        <input type="number" id="valor-unitario" name="valor_unitario"
                            class="form-control" min="0" step="0.01"
                            value="<?= $escapar($produtoEditar['valor_unitario'] ?? '0.00') ?>" required>
                    </div>

                    <div class="col-md-8">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea id="descricao" name="descricao" class="form-control" rows="3"
                            required><?= $escapar($produtoEditar['descricao'] ?? '') ?></textarea>
                    </div>

                    <div class="col-md-4">
                        <label for="imagem-arquivo" class="form-label">Imagem do produto</label>
                        <input
                            type="file"
                            id="imagem-arquivo"
                            name="imagem_arquivo"
                            class="form-control"
                            accept="image/jpeg,image/png,image/webp,image/gif"
                            <?= $produtoEditar ? '' : 'required' ?>
                        >
                        <div class="form-text">
                            jpg, png, webp ou gif, com até 5 mb.
                            <?php if ($produtoEditar): ?>
                                deixe vazio para manter a imagem atual.
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="categoria" class="form-label">Categoria</label>
                        <select id="categoria" name="id_categoria" class="form-select" required>
                            <option value="">Selecione uma categoria</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= (int) $categoria['id_categoria'] ?>"
                                    <?= (int) ($produtoEditar['id_categoria'] ?? 0) === (int) $categoria['id_categoria']
                                        ? 'selected'
                                        : '' ?>>
                                    <?= $escapar($categoria['nome_categoria']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <?= $produtoEditar ? 'Salvar alterações' : 'Adicionar produto' ?>
                        </button>

                        <?php if ($produtoEditar): ?>
                            <a href="catalogo2.php" class="btn btn-secondary">Cancelar</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </section>

        <section class="card shadow-sm border-0">
            <div class="table-responsive w-100">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Imagem</th>
                            <th>Produto</th>
                            <th>Categoria</th>
                            <th>Estoque</th>
                            <th>Valor unitário</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($produtos === []): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">Nenhum produto cadastrado.</td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($produtos as $produto): ?>
                            <tr>
                                <td>
                                    <img src="../../assets/img/<?= $escapar($produto['imagem']) ?>" width="70"
                                        alt="<?= $escapar($produto['nome_produto']) ?>">
                                </td>
                                <td><?= $escapar($produto['nome_produto']) ?></td>
                                <td><?= $escapar($produto['categorias']) ?></td>
                                <td><?= (int) $produto['quantidade_estoque'] ?></td>
                                <td class="text-nowrap">
                                    <?= $formatarMoeda((float) $produto['valor_unitario']) ?>
                                </td>
                                <td class="text-nowrap">
                                    <a href="catalogo2.php?editar=<?= (int) $produto['id_produto'] ?>"
                                        class="btn btn-warning btn-sm">Editar</a>

                                    <form method="post" class="d-inline"
                                        onsubmit="return confirm('Deseja excluir este produto e seu vínculo de categoria?')">
                                        <input type="hidden" name="csrf_token" value="<?= $escapar($csrfToken) ?>">
                                        <input type="hidden" name="acao" value="excluir">
                                        <input type="hidden" name="id_produto"
                                            value="<?= (int) $produto['id_produto'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <?php include '../../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
