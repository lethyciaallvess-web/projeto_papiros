"use strict";
let pagina = 1;
function elemento(id) {
    return document.getElementById(id);
}
function texto(id, valor) {
    const item = elemento(id);
    if (item) {
        item.textContent = valor;
    }
}
async function buscarDashboard(numeroPagina) {
    const busca = elemento("busca-produto");
    const categoria = elemento("filtro-categoria");
    const parametros = new URLSearchParams({
        busca: busca instanceof HTMLInputElement
            ? busca.value.trim()
            : "",
        categoria: categoria instanceof HTMLSelectElement
            ? categoria.value
            : "0",
        pagina: String(numeroPagina),
        limite: "10"
    });
    const resposta = await fetch(`../api/dashboard.php?${parametros}`);
    const dados = await resposta.json();
    if (!resposta.ok || !dados.sucesso) {
        throw new Error(dados.sucesso
            ? "Não foi possível acessar a API."
            : dados.mensagem);
    }
    return dados;
}
function calcularTotais(categorias) {
    return categorias.reduce((total, categoria) => ({
        produtos: total.produtos + categoria.total_produtos,
        estoque: total.estoque + categoria.estoque_total,
        criticos: total.criticos +
            categoria.produtos_estoque_critico,
        semEstoque: total.semEstoque +
            categoria.produtos_sem_estoque
    }), {
        produtos: 0,
        estoque: 0,
        criticos: 0,
        semEstoque: 0
    });
}
function preencherTabela(id, linhas, colunas, mensagem) {
    const tabela = elemento(id);
    if (!(tabela instanceof HTMLTableSectionElement)) {
        return;
    }
    tabela.replaceChildren();
    if (linhas.length === 0) {
        const linha = tabela.insertRow();
        const celula = linha.insertCell();
        celula.colSpan = colunas;
        celula.className = "text-center";
        celula.textContent = mensagem;
        return;
    }
    for (const valores of linhas) {
        const linha = tabela.insertRow();
        for (const valor of valores) {
            linha.insertCell().textContent = valor;
        }
    }
}
function preencherFiltro(categorias) {
    const campo = elemento("filtro-categoria");
    if (!(campo instanceof HTMLSelectElement) ||
        campo.options.length > 1) {
        return;
    }
    for (const categoria of categorias) {
        const opcao = document.createElement("option");
        opcao.value = String(categoria.id_categoria);
        opcao.textContent = categoria.nome_categoria;
        campo.appendChild(opcao);
    }
}
function renderizar(dados) {
    const totais = calcularTotais(dados.categorias);
    const destaque = dados.categorias.reduce((maior, categoria) => !maior ||
        categoria.estoque_total > maior.estoque_total
        ? categoria
        : maior, null);
    texto("categoria-destaque", destaque
        ? `${destaque.nome_categoria} (${destaque.estoque_total})`
        : "Sem dados");
    texto("total-produtos", String(totais.produtos));
    texto("estoque-total", String(totais.estoque));
    texto("total-criticos", String(totais.criticos));
    texto("total-sem-estoque", String(totais.semEstoque));
    preencherFiltro(dados.categorias);
    preencherTabela("tabela-categorias", dados.categorias.map((categoria) => [
        categoria.nome_categoria,
        String(categoria.total_produtos),
        String(categoria.estoque_total),
        categoria.media_estoque.toFixed(2),
        String(categoria.produtos_sem_estoque),
        String(categoria.produtos_estoque_critico),
        String(categoria.produtos_estoque_normal)
    ]), 7, "Nenhuma categoria encontrada.");
    const reposicao = dados.produtos
        .filter((produto) => produto.status_estoque !== "ESTOQUE NORMAL")
        .map((produto) => [
        produto.nome_produto,
        produto.categorias,
        String(produto.quantidade_estoque),
        produto.status_estoque
    ]);
    preencherTabela("tabela-reposicao", reposicao, 4, "Nenhum produto precisa de reposição.");
    pagina = dados.pagina;
    texto("pagina-atual", `Página ${pagina}`);
    const anterior = elemento("pagina-anterior");
    const proxima = elemento("proxima-pagina");
    if (anterior instanceof HTMLButtonElement) {
        anterior.disabled = pagina === 1;
    }
    if (proxima instanceof HTMLButtonElement) {
        proxima.disabled =
            dados.produtos.length < dados.limite;
    }
}
async function carregar(numeroPagina) {
    const mensagem = elemento("mensagem-dashboard");
    try {
        const dados = await buscarDashboard(numeroPagina);
        renderizar(dados);
        if (mensagem) {
            mensagem.className = dados.produtos.length
                ? "d-none"
                : "alert alert-warning";
            mensagem.textContent =
                "Nenhum produto encontrado.";
        }
    }
    catch (erro) {
        if (mensagem) {
            mensagem.className = "alert alert-danger";
            mensagem.textContent = erro instanceof Error
                ? erro.message
                : "Ocorreu um erro inesperado.";
        }
    }
}
function configurarEventos() {
    const formulario = elemento("filtros-dashboard");
    const anterior = elemento("pagina-anterior");
    const proxima = elemento("proxima-pagina");
    formulario?.addEventListener("submit", (evento) => {
        evento.preventDefault();
        void carregar(1);
    });
    anterior?.addEventListener("click", () => {
        void carregar(Math.max(1, pagina - 1));
    });
    proxima?.addEventListener("click", () => {
        void carregar(pagina + 1);
    });
}
configurarEventos();
void carregar(1);
