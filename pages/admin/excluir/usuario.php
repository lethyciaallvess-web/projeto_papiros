<?php

require_once '../../../config/conexao.php';

$id = $_GET['id'] ?? null;

if ($id) {

    $sql = "DELETE FROM usuario WHERE id_usuario = :id";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':id' => $id
    ]);
}

header('Location: ../usuario.php');
exit;