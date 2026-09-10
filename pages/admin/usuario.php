<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/autenticacao.php';

$usuarioLogado = exigirAutenticacao('../login.php');

require_once __DIR__ . '/../../config/conexao.php';

$usuarios = $pdo->query(
    'SELECT id_usuario, email
    FROM usuario
    ORDER BY email'
)->fetchAll(PDO::FETCH_ASSOC);

$mensagens = [
    'salvo' => ['success', 'Usuário salvo com sucesso.'],
    'excluido' => ['success', 'Usuário excluído com sucesso.'],
    'invalido' => ['warning', 'Informe um e-mail válido e uma senha com pelo menos 6 caracteres.'],
    'duplicado' => ['warning', 'Já existe um usuário com esse e-mail.'],
    'nao_encontrado' => ['warning', 'O usuário solicitado não foi encontrado.'],
    'proprio_usuario' => ['warning', 'Você não pode excluir o usuário que está conectado.'],
    'ultimo_usuario' => ['warning', 'O último usuário do sistema não pode ser excluído.'],
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
    <title>Usuários - Papiro's</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>
    <?php include __DIR__ . '/../../includes/header2.php'; ?>

    <main class="admin">
        <div class="container py-5">
            <div class="admin-cabecalho">
                <div>
                    <h1>Usuários</h1>
                    <p>Cadastro, consulta, edição e exclusão de usuários.</p>
                </div>

                <a href="salvar/usuario.php" class="btn btn-cadastrar">
                    + cadastrar usuário
                </a>
            </div>

            <?php if ($mensagemAtual): ?>
                <div class="alert alert-<?= $mensagemAtual[0] ?>" role="alert">
                    <?= $escapar($mensagemAtual[1]) ?>
                </div>
            <?php endif; ?>

            <div class="admin-card">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th scope="col">id</th>
                                <th scope="col">e-mail</th>
                                <th scope="col">ações</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if ($usuarios === []): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-secondary py-4">
                                        nenhum usuário encontrado.
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <?php foreach ($usuarios as $usuario): ?>
                                <?php
                                $idUsuario = (int) $usuario['id_usuario'];
                                $eUsuarioLogado = $idUsuario === (int) $usuarioLogado['id'];
                                ?>
                                <tr>
                                    <td><?= $idUsuario ?></td>
                                    <td>
                                        <?= $escapar($usuario['email']) ?>

                                        <?php if ($eUsuarioLogado): ?>
                                            <span class="badge text-bg-primary ms-2">você</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-2">
                                            <a
                                                href="salvar/usuario.php?id=<?= $idUsuario ?>"
                                                class="btn btn-editar"
                                            >
                                                editar
                                            </a>

                                            <?php if (!$eUsuarioLogado): ?>
                                                <form
                                                    method="post"
                                                    action="excluir/usuario.php"
                                                    onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');"
                                                >
                                                    <input
                                                        type="hidden"
                                                        name="csrf_token"
                                                        value="<?= $escapar(tokenCsrf()) ?>"
                                                    >
                                                    <input
                                                        type="hidden"
                                                        name="id"
                                                        value="<?= $idUsuario ?>"
                                                    >
                                                    <button type="submit" class="btn btn-excluir">
                                                        excluir
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <button
                                                    type="button"
                                                    class="btn btn-secondary"
                                                    disabled
                                                    title="O usuário conectado não pode ser excluído."
                                                >
                                                    excluir
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/../../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
