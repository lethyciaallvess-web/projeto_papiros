"use strict";
function elemento(id) {
    return document.getElementById(id);
}
function alterarTexto(id, texto) {
    const item = elemento(id);
    if (item) {
        item.textContent = texto;
    }
}
async function buscarDashboard() {
    const resposta = await fetch("../api/dashboard.php");
    if (!resposta.ok) {
        throw new Error("Não foi possível acessar a API.");
    }
    const dados = await resposta.json();
    if (!dados.sucesso) {
        throw new Error(dados.mensagem ?? "Não foi possível carregar os dados.");
    }
    return dados;
}
function calcularTotais(produtos) {
    return produtos.reduce((total, produto) => {
        total.produtos++;
        total.estoque += Number(produto.quantidade_estoque) || 0;
        if (produto.status_estoque === "ESTOQUE CRÍTICO") {
            total.criticos++;
        }
        if (produto.status_estoque === "SEM ESTOQUE") {
            total.semEstoque++;
        }
        return total;
    }, {
        produtos: 0,
        estoque: 0,
        criticos: 0,
        semEstoque: 0
    });
}
function adicionarCelula(linha, valor) {
    const celula = linha.insertCell();
    celula.textContent = valor;
}
function renderizarCategorias(categorias) {
    const tabela = elemento("tabela-categorias");
    if (!(tabela instanceof HTMLTableSectionElement)) {
        return;
    }
    tabela.replaceChildren();
    if (categorias.length === 0) {
        tabela.innerHTML =
            '<tr><td colspan="7" class="text-center">' +
                "Nenhuma categoria encontrada.</td></tr>";
        return;
    }
    categorias.forEach((categoria) => {
        const linha = tabela.insertRow();
        adicionarCelula(linha, categoria.nome_categoria);
        adicionarCelula(linha, String(categoria.total_produtos));
        adicionarCelula(linha, String(categoria.estoque_total));
        adicionarCelula(linha, categoria.media_estoque.toFixed(2));
        adicionarCelula(linha, String(categoria.produtos_sem_estoque));
        adicionarCelula(linha, String(categoria.produtos_estoque_critico));
        adicionarCelula(linha, String(categoria.produtos_estoque_normal));
    });
}
function renderizarReposicao(produtos) {
    const tabela = elemento("tabela-reposicao");
    if (!(tabela instanceof HTMLTableSectionElement)) {
        return;
    }
    tabela.replaceChildren();
    const reposicao = produtos.filter((produto) => produto.status_estoque !== "ESTOQUE NORMAL");
    if (reposicao.length === 0) {
        tabela.innerHTML =
            '<tr><td colspan="4" class="text-center">' +
                "Nenhum produto precisa de reposição.</td></tr>";
        return;
    }
    reposicao.forEach((produto) => {
        const linha = tabela.insertRow();
        adicionarCelula(linha, produto.nome_produto);
        adicionarCelula(linha, produto.categorias);
        adicionarCelula(linha, String(produto.quantidade_estoque));
        adicionarCelula(linha, produto.status_estoque);
    });
}
function renderizarIndicadores(produtos) {
    const totais = calcularTotais(produtos);
    alterarTexto("total-produtos", String(totais.produtos));
    alterarTexto("estoque-total", String(totais.estoque));
    alterarTexto("total-criticos", String(totais.criticos));
    alterarTexto("total-sem-estoque", String(totais.semEstoque));
}
async function iniciarDashboard() {
    const mensagem = elemento("mensagem-dashboard");
    try {
        const dados = await buscarDashboard();
        if (dados.produtos.length === 0) {
            throw new Error("Nenhum dado registrado.");
        }
        renderizarIndicadores(dados.produtos);
        renderizarCategorias(dados.categorias);
        renderizarReposicao(dados.produtos);
        if (mensagem) {
            mensagem.classList.add("d-none");
        }
    }
    catch (erro) {
        const texto = erro instanceof Error
            ? erro.message
            : "Ocorreu um erro inesperado.";
        if (mensagem) {
            mensagem.className = "alert alert-danger";
            mensagem.textContent = texto;
        }
    }
}
void iniciarDashboard();
