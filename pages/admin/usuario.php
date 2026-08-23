<?php

session_start();

if (!isset($_SESSION['projeto_papiros'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../../config/conexao.php';

$sql = "SELECT id_usuario, email FROM usuario ORDER BY id_usuario";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" href="../../assets/img/icone.png">

    <title>Administração - Papiro's</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../../assets/css/style.css">

</head>

<body>

    <?php include __DIR__ . '/../../includes/header2.php'; ?>

    <main class="admin">

        <div class="container py-5">

            <div class="admin-cabecalho">

                <div>
                    <h1>Administração</h1>

                    <p>
                        Gerenciamento de usuários
                    </p>
                </div>

                <a href="salvar/usuario.php" class="btn btn-cadastrar">
                    + Cadastrar usuário
                </a>

            </div>


            <div class="admin-card">

                <div class="table-responsive">

                    <table class="table align-middle">

                        <thead>

                            <tr>
                                <th>ID</th>
                                <th>E-mail</th>
                                <th>Ações</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($usuarios as $usuario) { ?>

                                <tr>

                                    <td>
                                        <?= $usuario['id_usuario'] ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($usuario['email']) ?>
                                    </td>

                                    <td>

                                        <a href="salvar/usuario.php?id=<?= $usuario['id_usuario'] ?>"
                                            class="btn btn-editar">

                                            Editar

                                        </a>

                                        <a href="excluir/usuario.php?id=<?= $usuario['id_usuario'] ?>"
                                            class="btn btn-excluir"
                                            onclick="return confirm('Tem certeza que deseja excluir este usuário?')">

                                            Excluir

                                        </a>

                                    </td>

                                </tr>

                            <?php } ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </main>


    <?php include("../../includes/footer.php"); ?>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>