<?php
session_start();

if (!isset($_SESSION['projeto_papiros'])) {
    header('Location: ../../login.php');
    exit;
}

require_once '../../../config/conexao.php';

$id = $_GET['id'] ?? null;
$usuario = [];

if ($id) {
    $stmt = $pdo->prepare("SELECT id_usuario, email FROM usuario WHERE id_usuario = :id");
    $stmt->execute(['id' => $id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = $_POST['id'] ?? null;
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    if ($id) {

        if ($senha) {
            $sql = "UPDATE usuario SET email = :email, senha = :senha WHERE id_usuario = :id";
            $dados = [
                'email' => $email,
                'senha' => password_hash($senha, PASSWORD_DEFAULT),
                'id' => $id
            ];
        } else {
            $sql = "UPDATE usuario SET email = :email WHERE id_usuario = :id";
            $dados = ['email' => $email, 'id' => $id];
        }

    } else {

        $sql = "INSERT INTO usuario (email, senha) VALUES (:email, :senha)";
        $dados = [
            'email' => $email,
            'senha' => password_hash($senha, PASSWORD_DEFAULT)
        ];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($dados);

    header('Location: ../usuario.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $id ? 'Editar usuário' : 'Cadastrar usuário' ?> - Papiro's</title>

    <link rel="icon" href="../../../assets/img/icone.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../assets/css/style.css">
</head>

<body>

<?php include '../../../includes/header2.php'; ?>

<main class="container py-5">

    <h1 class="mb-4">
        <?= $id ? 'Editar usuário' : 'Cadastrar usuário' ?>
    </h1>

    <form method="post" class="card p-4">

        <input type="hidden" name="id" value="<?= $id ?>">

        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>

            <input
                type="email"
                id="email"
                name="email"
                class="form-control"
                value="<?= htmlspecialchars($usuario['email'] ?? '') ?>"
                required
            >
        </div>

        <div class="mb-3">
            <label for="senha" class="form-label">Senha</label>

            <input
                type="password"
                id="senha"
                name="senha"
                class="form-control"
                <?= $id ? '' : 'required' ?>
            >

            <?php if ($id): ?>
                <small class="text-muted">
                    Deixe vazio para manter a senha atual.
                </small>
            <?php endif; ?>
        </div>

        <div>
            <button type="submit" class="btn btn-success">
                <?= $id ? 'Salvar alterações' : 'Cadastrar' ?>
            </button>

            <a href="../usuario.php" class="btn btn-secondary">
                Voltar
            </a>
        </div>

    </form>

</main>

<?php include '../../../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>