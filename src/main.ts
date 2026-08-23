interface Categoria {
    nome_categoria: string;
    total_produtos: number;
    estoque_total: number;
    media_estoque: number;
    produtos_sem_estoque: number;
    produtos_estoque_critico: number;
    produtos_estoque_normal: number;
}

interface Produto {
    nome_produto: string;
    quantidade_estoque: number;
    status_estoque: string;
    categorias: string;
}

interface RespostaAPI {
    sucesso: boolean;
    categorias: Categoria[];
    produtos: Produto[];
    mensagem?: string;
}

interface Totais {
    produtos: number;
    estoque: number;
    criticos: number;
    semEstoque: number;
}

function elemento(id: string): HTMLElement | null {
    return document.getElementById(id);
}

function alterarTexto(id: string, texto: string): void {
    const item = elemento(id);

    if (item) {
        item.textContent = texto;
    }
}

async function buscarDashboard(): Promise<RespostaAPI> {
    const resposta = await fetch("../api/dashboard.php");

    if (!resposta.ok) {
        throw new Error("Não foi possível acessar a API.");
    }

    const dados = await resposta.json() as RespostaAPI;

    if (!dados.sucesso) {
        throw new Error(
            dados.mensagem ?? "Não foi possível carregar os dados."
        );
    }

    return dados;
}

function calcularTotais(produtos: Produto[]): Totais {
    return produtos.reduce<Totais>(
        (total, produto) => {
            total.produtos++;
            total.estoque += Number(produto.quantidade_estoque) || 0;

            if (produto.status_estoque === "ESTOQUE CRÍTICO") {
                total.criticos++;
            }

            if (produto.status_estoque === "SEM ESTOQUE") {
                total.semEstoque++;
            }

            return total;
        },
        {
            produtos: 0,
            estoque: 0,
            criticos: 0,
            semEstoque: 0
        }
    );
}

function adicionarCelula(
    linha: HTMLTableRowElement,
    valor: string
): void {
    const celula = linha.insertCell();
    celula.textContent = valor;
}

function renderizarCategorias(categorias: Categoria[]): void {
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
        adicionarCelula(
            linha,
            categoria.media_estoque.toFixed(2)
        );
        adicionarCelula(
            linha,
            String(categoria.produtos_sem_estoque)
        );
        adicionarCelula(
            linha,
            String(categoria.produtos_estoque_critico)
        );
        adicionarCelula(
            linha,
            String(categoria.produtos_estoque_normal)
        );
    });
}

function renderizarReposicao(produtos: Produto[]): void {
    const tabela = elemento("tabela-reposicao");

    if (!(tabela instanceof HTMLTableSectionElement)) {
        return;
    }

    tabela.replaceChildren();

    const reposicao = produtos.filter(
        (produto) => produto.status_estoque !== "ESTOQUE NORMAL"
    );

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
        adicionarCelula(
            linha,
            String(produto.quantidade_estoque)
        );
        adicionarCelula(linha, produto.status_estoque);
    });
}

function renderizarIndicadores(produtos: Produto[]): void {
    const totais = calcularTotais(produtos);

    alterarTexto("total-produtos", String(totais.produtos));
    alterarTexto("estoque-total", String(totais.estoque));
    alterarTexto("total-criticos", String(totais.criticos));
    alterarTexto("total-sem-estoque", String(totais.semEstoque));
}

async function iniciarDashboard(): Promise<void> {
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
    } catch (erro: unknown) {
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