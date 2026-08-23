<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">

    <link rel="icon" href="../../assets/img/icone.png">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Contato - Papiro's</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../../assets/css/contato.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

    <?php include("../../includes/header.php"); ?>

    <main class="container py-5">

        <h1 class="mb-4">Entre em Contato</h1>

        <form action="enviar.php" method="POST">

            <div class="mb-3">
                <label class="form-label">Nome Completo</label>
                <input type="text" class="form-control" name="nome" required>
            </div>

            <div class="mb-3">
                <label class="form-label">E-mail</label>
                <input type="email" class="form-control" name="email" required>
            </div>

            <div class="mb-3">
                <label class="form-label">WhatsApp</label>
                <input type="tel" class="form-control" name="whatsapp" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Mensagem</label>
                <textarea class="form-control" name="mensagem" rows="5" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary">
                Enviar
            </button>

        </form>

    </main>

    <?php include("../../includes/footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>