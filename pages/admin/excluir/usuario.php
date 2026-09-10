<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../includes/autenticacao.php';

$usuarioLogado = exigirAutenticacao('../../login.php');

require_once __DIR__ . '/../../../config/conexao.php';

$redirecionar = static function (string $mensagem): never {
    header('Location: ../usuario.php?msg=' . urlencode($mensagem));
    exit;
};

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $redirecionar('erro');
}

if (!tokenCsrfValido($_POST['csrf_token'] ?? null)) {
    $redirecionar('csrf');
}

$idUsuario = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;

if ($idUsuario <= 0) {
    $redirecionar('nao_encontrado');
}

if ($idUsuario === (int) $usuarioLogado['id']) {
    $redirecionar('proprio_usuario');
}

$totalUsuarios = (int) $pdo->query(
    'SELECT COUNT(*) FROM usuario'
)->fetchColumn();

if ($totalUsuarios <= 1) {
    $redirecionar('ultimo_usuario');
}

try {
    $excluir = $pdo->prepare(
        'DELETE FROM usuario WHERE id_usuario = ?'
    );
    $excluir->execute([$idUsuario]);

    $redirecionar(
        $excluir->rowCount() > 0 ? 'excluido' : 'nao_encontrado'
    );
} catch (PDOException $erroBanco) {
    error_log($erroBanco->getMessage());
    $redirecionar('erro');
}
