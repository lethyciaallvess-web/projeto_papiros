<?php

function filtrarProdutosPorCategoria(array $produtos, string $categoria): array
{

    $filtrados = [];

    foreach ($produtos as $produto) {

        if (stripos($produto['nome_produto'], $categoria) !== false) {

            $filtrados[] = $produto;
        }
    }

    return $filtrados;
}

function contarProdutosPorCategoria(array $produtos, string $categoria): int
{

    return count(filtrarProdutosPorCategoria($produtos, $categoria));
}

function validarProdutos(array $produtos): bool
{

    if (empty($produtos)) {

        return false;
    }

    foreach ($produtos as $produto) {

        if (!isset($produto['nome_produto']) || trim($produto['nome_produto']) === '') {

            return false;
        }
    }

    return true;
}