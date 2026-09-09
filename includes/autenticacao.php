<?php

declare(strict_types=1);

function exigirAutenticacao(string $destinoLogin): array
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $usuario = $_SESSION['projeto_papiros'] ?? null;

    if (!is_array($usuario) || !isset($usuario['id'], $usuario['email'])) {
        header("Location: {$destinoLogin}");
        exit;
    }

    return $usuario;
}

function tokenCsrf(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return (string) $_SESSION['csrf_token'];
}

function tokenCsrfValido(?string $token): bool
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $tokenSessao = $_SESSION['csrf_token'] ?? '';

    return is_string($token)
        && is_string($tokenSessao)
        && $tokenSessao !== ''
        && hash_equals($tokenSessao, $token);
}
