<?php
session_start();
require_once '../config/conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    $stmt = $pdo->prepare(
        "SELECT id_usuario, email, senha FROM usuario WHERE email = :email"
    );

    $stmt->execute(['email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha, $usuario['senha'])) {

        $_SESSION['projeto_papiros'] = [
            'id' => $usuario['id_usuario'],
            'email' => $usuario['email']
        ];

        header('Location: index2.php');
        exit;
    }

    $erro = 'E-mail ou senha inválidos.';
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Papiro's</title>

    <link rel="icon" href="../assets/img/icone.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/login.css">
</head>

<body>

    <main class="login">

        <div class="login-box">

            <img src="../assets/img/logo.png" alt="Logo Papiro's" class="logo">

            <h2>Login</h2>

            <?php if ($erro): ?>
                <div class="alert alert-danger"><?= $erro ?></div>
            <?php endif; ?>

            <form method="post">

                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" id="senha" name="senha" class="form-control" required>
                </div>

                <button type="submit" class="btn-login">Entrar</button>

            </form>

        </div>

    </main>

</body>

</html>