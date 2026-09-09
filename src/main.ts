type StatusEstoque =
    | "SEM ESTOQUE"
    | "ESTOQUE CRÍTICO"
    | "ESTOQUE NORMAL";

interface Categoria {
    id_categoria: number;
    nome_categoria: string;
    total_produtos: number;
    estoque_total: number;
    media_estoque: number;
    valor_total_estoque: number;
    produtos_sem_estoque: number;
    produtos_estoque_critico: number;
    produtos_estoque_normal: number;
}

interface ItemInventario {
    id_produto: number;
    quantidade_estoque: number;
    valor_unitario: number;
    valor_total: number;
    status_estoque: StatusEstoque;
}

interface Produto extends ItemInventario {
    nome_produto: string;
    categorias: string;
}

interface RespostaSucesso {
    sucesso: true;
    pagina: number;
    limite: number;
    total_registros: number;
    total_paginas: number;
    categorias: Categoria[];
    inventario: ItemInventario[];
    produtos: Produto[];
}

interface RespostaErro {
    sucesso: false;
    mensagem: string;
}

type RespostaAPI = RespostaSucesso | RespostaErro;

interface Totais {
    produtos: number;
    estoque: number;
    valorEstoque: number;
    criticos: number;
    semEstoque: number;
}

let pagina = 1;

const formatadorMoeda = new Intl.NumberFormat("pt-BR", {
    style: "currency",
    currency: "BRL"
});

function ehObjeto(valor: unknown): valor is Record<string, unknown> {
    return typeof valor === "object" && valor !== null;
}

function ehNumero(valor: unknown): valor is number {
    return typeof valor === "number" && Number.isFinite(valor);
}

function ehStatusEstoque(valor: unknown): valor is StatusEstoque {
    return valor === "SEM ESTOQUE"
        || valor === "ESTOQUE CRÍTICO"
        || valor === "ESTOQUE NORMAL";
}

function ehCategoria(valor: unknown): valor is Categoria {
    return ehObjeto(valor)
        && ehNumero(valor.id_categoria)
        && typeof valor.nome_categoria === "string"
        && ehNumero(valor.total_produtos)
        && ehNumero(valor.estoque_total)
        && ehNumero(valor.media_estoque)
        && ehNumero(valor.valor_total_estoque)
        && ehNumero(valor.produtos_sem_estoque)
        && ehNumero(valor.produtos_estoque_critico)
        && ehNumero(valor.produtos_estoque_normal);
}

function ehItemInventario(valor: unknown): valor is ItemInventario {
    return ehObjeto(valor)
        && ehNumero(valor.id_produto)
        && ehNumero(valor.quantidade_estoque)
        && ehNumero(valor.valor_unitario)
        && ehNumero(valor.valor_total)
        && ehStatusEstoque(valor.status_estoque);
}

function ehProduto(valor: unknown): valor is Produto {
    return ehObjeto(valor)
        && typeof valor.nome_produto === "string"
        && typeof valor.categorias === "string"
        && ehItemInventario(valor);
}

function ehRespostaAPI(valor: unknown): valor is RespostaAPI {
    if (!ehObjeto(valor) || typeof valor.sucesso !== "boolean") {
        return false;
    }

    if (valor.sucesso === false) {
        return typeof valor.mensagem === "string";
    }

    return ehNumero(valor.pagina)
        && ehNumero(valor.limite)
        && ehNumero(valor.total_registros)
        && ehNumero(valor.total_paginas)
        && Array.isArray(valor.categorias)
        && valor.categorias.every(ehCategoria)
        && Array.isArray(valor.inventario)
        && valor.inventario.every(ehItemInventario)
        && Array.isArray(valor.produtos)
        && valor.produtos.every(ehProduto);
}

function elemento(id: string): HTMLElement | null {
    return document.getElementById(id);
}

function texto(id: string, valor: string): void {
    const item = elemento(id);

    if (item) {
        item.textContent = valor;
    }
}

async function buscarDashboard(
    numeroPagina: number
): Promise<RespostaSucesso> {
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

    const resposta = await fetch(
        `../api/dashboard.php?${parametros}`
    );

    const conteudo: unknown = await resposta.json();

    if (!ehRespostaAPI(conteudo)) {
        throw new Error("A API retornou dados em formato inválido.");
    }

    if (!resposta.ok || !conteudo.sucesso) {
        throw new Error(
            conteudo.sucesso
                ? "Não foi possível acessar a API."
                : conteudo.mensagem
        );
    }

    return conteudo;
}

function calcularTotais(
    inventario: ItemInventario[]
): Totais {
    return inventario.reduce<Totais>(
        (total, produto) => ({
            produtos: total.produtos + 1,
            estoque:
                total.estoque + produto.quantidade_estoque,
            valorEstoque:
                total.valorEstoque
                + produto.quantidade_estoque
                * produto.valor_unitario,
            criticos:
                total.criticos
                + (produto.status_estoque === "ESTOQUE CRÍTICO" ? 1 : 0),
            semEstoque:
                total.semEstoque
                + (produto.status_estoque === "SEM ESTOQUE" ? 1 : 0)
        }),
        {
            produtos: 0,
            estoque: 0,
            valorEstoque: 0,
            criticos: 0,
            semEstoque: 0
        }
    );
}

function preencherTabela(
    id: string,
    linhas: string[][],
    colunas: number,
    mensagem: string
): void {
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

function preencherFiltro(
    categorias: Categoria[]
): void {
    const campo = elemento("filtro-categoria");

    if (
        !(campo instanceof HTMLSelectElement)
        || campo.options.length > 1
    ) {
        return;
    }

    for (const categoria of categorias) {
        const opcao = document.createElement("option");

        opcao.value = String(categoria.id_categoria);
        opcao.textContent = categoria.nome_categoria;

        campo.appendChild(opcao);
    }
}

function renderizar(dados: RespostaSucesso): void {
    const totais = calcularTotais(dados.inventario);
    const destaque = dados.categorias.reduce<Categoria | null>(
        (maior, categoria) =>
            !maior
                || categoria.valor_total_estoque
                > maior.valor_total_estoque
                ? categoria
                : maior,
        null
    );

    texto(
        "categoria-destaque",
        destaque
            ? `${destaque.nome_categoria} (${formatadorMoeda.format(
                destaque.valor_total_estoque
            )})`
            : "Sem dados"
    );

    texto("total-produtos", String(totais.produtos));
    texto("estoque-total", String(totais.estoque));
    texto(
        "valor-total-estoque",
        formatadorMoeda.format(totais.valorEstoque)
    );
    texto("total-criticos", String(totais.criticos));
    texto("total-sem-estoque", String(totais.semEstoque));

    preencherFiltro(dados.categorias);

    preencherTabela(
        "tabela-categorias",
        dados.categorias.map((categoria) => [
            categoria.nome_categoria,
            String(categoria.total_produtos),
            String(categoria.estoque_total),
            categoria.media_estoque.toFixed(2),
            formatadorMoeda.format(categoria.valor_total_estoque),
            String(categoria.produtos_sem_estoque),
            String(categoria.produtos_estoque_critico),
            String(categoria.produtos_estoque_normal)
        ]),
        8,
        "Nenhuma categoria encontrada."
    );

    const reposicao = dados.produtos
        .filter(
            (produto) =>
                produto.status_estoque !== "ESTOQUE NORMAL"
        )
        .map((produto) => [
            produto.nome_produto,
            produto.categorias,
            String(produto.quantidade_estoque),
            formatadorMoeda.format(produto.valor_unitario),
            formatadorMoeda.format(produto.valor_total),
            produto.status_estoque
        ]);

    preencherTabela(
        "tabela-reposicao",
        reposicao,
        6,
        "Nenhum produto precisa de reposição."
    );

    pagina = dados.pagina;

    texto(
        "pagina-atual",
        `Página ${pagina} de ${Math.max(1, dados.total_paginas)}`
    );

    const anterior = elemento("pagina-anterior");
    const proxima = elemento("proxima-pagina");

    if (anterior instanceof HTMLButtonElement) {
        anterior.disabled = pagina <= 1;
    }

    if (proxima instanceof HTMLButtonElement) {
        proxima.disabled = dados.total_paginas === 0
            || pagina >= dados.total_paginas;
    }
}

async function carregar(numeroPagina: number): Promise<void> {
    const mensagem = elemento("mensagem-dashboard");

    try {
        const dados = await buscarDashboard(numeroPagina);

        renderizar(dados);

        if (mensagem) {
            mensagem.className = dados.total_registros > 0
                ? "d-none"
                : "alert alert-warning";

            mensagem.textContent =
                "Nenhum produto encontrado.";
        }
    } catch (erro: unknown) {
        if (mensagem) {
            mensagem.className = "alert alert-danger";
            mensagem.textContent = erro instanceof Error
                ? erro.message
                : "Ocorreu um erro inesperado.";
        }
    }
}

function configurarEventos(): void {
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
