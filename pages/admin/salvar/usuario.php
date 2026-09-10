<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../includes/autenticacao.php';

$usuarioLogado = exigirAutenticacao('../../login.php');

require_once __DIR__ . '/../../../config/conexao.php';

$redirecionar = static function (string $mensagem): never {
    header('Location: ../usuario.php?msg=' . urlencode($mensagem));
    exit;
};

$idUsuario = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$usuario = null;
$erro = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idUsuario = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
    $email = trim((string) ($_POST['email'] ?? ''));
    $senha = (string) ($_POST['senha'] ?? '');

    if (!tokenCsrfValido($_POST['csrf_token'] ?? null)) {
        $redirecionar('csrf');
    }

    $emailValido = filter_var($email, FILTER_VALIDATE_EMAIL) !== false
        && mb_strlen($email) <= 100;
    $senhaValida = $idUsuario > 0
        ? ($senha === '' || mb_strlen($senha) >= 6)
        : mb_strlen($senha) >= 6;

    if (!$emailValido || !$senhaValida) {
        $erro = 'Informe um e-mail válido e uma senha com pelo menos 6 caracteres.';
    } else {
        $verificarEmail = $pdo->prepare(
            'SELECT COUNT(*)
            FROM usuario
            WHERE email = ? AND id_usuario <> ?'
        );
        $verificarEmail->execute([$email, $idUsuario]);

        if ((int) $verificarEmail->fetchColumn() > 0) {
            $erro = 'Já existe um usuário com esse e-mail.';
        } else {
            try {
                if ($idUsuario > 0) {
                    $verificarId = $pdo->prepare(
                        'SELECT COUNT(*) FROM usuario WHERE id_usuario = ?'
                    );
                    $verificarId->execute([$idUsuario]);

                    if ((int) $verificarId->fetchColumn() === 0) {
                        $redirecionar('nao_encontrado');
                    }

                    if ($senha !== '') {
                        $salvar = $pdo->prepare(
                            'UPDATE usuario
                            SET email = ?, senha = ?
                            WHERE id_usuario = ?'
                        );
                        $salvar->execute([
                            $email,
                            password_hash($senha, PASSWORD_DEFAULT),
                            $idUsuario
                        ]);
                    } else {
                        $salvar = $pdo->prepare(
                            'UPDATE usuario SET email = ? WHERE id_usuario = ?'
                        );
                        $salvar->execute([$email, $idUsuario]);
                    }

                    if ($idUsuario === (int) $usuarioLogado['id']) {
                        $_SESSION['projeto_papiros']['email'] = $email;
                    }
                } else {
                    $salvar = $pdo->prepare(
                        'INSERT INTO usuario (email, senha) VALUES (?, ?)'
                    );
                    $salvar->execute([
                        $email,
                        password_hash($senha, PASSWORD_DEFAULT)
                    ]);
                }

                $redirecionar('salvo');
            } catch (PDOException $erroBanco) {
                error_log($erroBanco->getMessage());
                $erro = 'Não foi possível salvar o usuário.';
            }
        }
    }
}

if ($idUsuario > 0) {
    $consulta = $pdo->prepare(
        'SELECT id_usuario, email FROM usuario WHERE id_usuario = ?'
    );
    $consulta->execute([$idUsuario]);
    $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        $redirecionar('nao_encontrado');
    }

    if ($email === '') {
        $email = (string) $usuario['email'];
    }
}

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
    <title><?= $idUsuario > 0 ? 'Editar usuário' : 'Cadastrar usuário' ?> - Papiro's</title>
    <link rel="icon" href="../../../assets/img/icone.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../assets/css/style.css">
</head>

<body>
    <?php include __DIR__ . '/../../../includes/header2.php'; ?>

    <main class="container py-5">
        <h1 class="mb-4">
            <?= $idUsuario > 0 ? 'Editar usuário' : 'Cadastrar usuário' ?>
        </h1>

        <?php if ($erro !== ''): ?>
            <div class="alert alert-danger" role="alert">
                <?= $escapar($erro) ?>
            </div>
        <?php endif; ?>

        <form method="post" class="card p-4" novalidate>
            <input type="hidden" name="csrf_token" value="<?= $escapar(tokenCsrf()) ?>">
            <input type="hidden" name="id" value="<?= $idUsuario ?>">

            <div class="mb-3">
                <label for="email" class="form-label">e-mail</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control"
                    maxlength="100"
                    value="<?= $escapar($email) ?>"
                    required
                >
            </div>

            <div class="mb-3">
                <label for="senha" class="form-label">senha</label>
                <input
                    type="password"
                    id="senha"
                    name="senha"
                    class="form-control"
                    minlength="6"
                    <?= $idUsuario > 0 ? '' : 'required' ?>
                >

                <div class="form-text">
                    <?php if ($idUsuario > 0): ?>
                        deixe vazio para manter a senha atual.
                    <?php else: ?>
                        use pelo menos 6 caracteres.
                    <?php endif; ?>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <?= $idUsuario > 0 ? 'salvar alterações' : 'cadastrar' ?>
                </button>

                <a href="../usuario.php" class="btn btn-secondary">voltar</a>
            </div>
        </form>
    </main>

    <?php include __DIR__ . '/../../../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
