USE projeto_papiros;

-- =====================================================
-- 1. CTE: ANALISE DE ESTOQUE POR CATEGORIA
-- =====================================================

WITH estoque_por_categoria AS (
    SELECT
        c.id_categoria,
        c.nome_categoria,
        COUNT(DISTINCT p.id_produto) AS total_produtos,
        COALESCE(SUM(p.quantidade_estoque), 0) AS estoque_total,
        COALESCE(AVG(p.quantidade_estoque), 0) AS media_estoque,
        COUNT(DISTINCT CASE
            WHEN p.quantidade_estoque = 0 THEN p.id_produto
        END) AS produtos_sem_estoque,
        COUNT(DISTINCT CASE
            WHEN p.quantidade_estoque BETWEEN 1 AND 5 THEN p.id_produto
        END) AS produtos_estoque_critico,
        COUNT(DISTINCT CASE
            WHEN p.quantidade_estoque > 5 THEN p.id_produto
        END) AS produtos_estoque_normal
    FROM categoria AS c
    LEFT JOIN produto_categoria AS pc
        ON pc.id_categoria = c.id_categoria
    LEFT JOIN produto AS p
        ON p.id_produto = pc.id_produto
    GROUP BY c.id_categoria, c.nome_categoria
)
SELECT
    id_categoria,
    nome_categoria,
    total_produtos,
    estoque_total,
    ROUND(media_estoque, 2) AS media_estoque,
    produtos_sem_estoque,
    produtos_estoque_critico,
    produtos_estoque_normal
FROM estoque_por_categoria
ORDER BY nome_categoria;

-- =====================================================
-- 2. VIEW: INDICADORES DE ESTOQUE POR CATEGORIA
-- =====================================================

CREATE OR REPLACE VIEW vw_estoque_por_categoria AS
SELECT
    c.id_categoria,
    c.nome_categoria,
    COUNT(DISTINCT p.id_produto) AS total_produtos,
    COALESCE(SUM(p.quantidade_estoque), 0) AS estoque_total,
    ROUND(COALESCE(AVG(p.quantidade_estoque), 0), 2) AS media_estoque,
    COUNT(DISTINCT CASE
        WHEN p.quantidade_estoque = 0 THEN p.id_produto
    END) AS produtos_sem_estoque,
    COUNT(DISTINCT CASE
        WHEN p.quantidade_estoque BETWEEN 1 AND 5 THEN p.id_produto
    END) AS produtos_estoque_critico,
    COUNT(DISTINCT CASE
        WHEN p.quantidade_estoque > 5 THEN p.id_produto
    END) AS produtos_estoque_normal
FROM categoria AS c
LEFT JOIN produto_categoria AS pc
    ON pc.id_categoria = c.id_categoria
LEFT JOIN produto AS p
    ON p.id_produto = pc.id_produto
GROUP BY c.id_categoria, c.nome_categoria;

-- =====================================================
-- 3. VIEW: PRODUTOS COM CATEGORIA E STATUS DE ESTOQUE
-- =====================================================

CREATE OR REPLACE VIEW vw_produtos_estoque AS
SELECT
    p.id_produto,
    p.nome_produto,
    p.descricao,
    p.imagem,
    p.quantidade_estoque,
    CASE
        WHEN p.quantidade_estoque = 0 THEN 'SEM ESTOQUE'
        WHEN p.quantidade_estoque BETWEEN 1 AND 5 THEN 'ESTOQUE CRÍTICO'
        ELSE 'ESTOQUE NORMAL'
    END AS status_estoque,
    COUNT(DISTINCT c.id_categoria) AS total_categorias,
    COALESCE(
        GROUP_CONCAT(
            DISTINCT c.nome_categoria
            ORDER BY c.nome_categoria
            SEPARATOR ', '
        ),
        'SEM CATEGORIA'
    ) AS categorias
FROM produto AS p
LEFT JOIN produto_categoria AS pc
    ON pc.id_produto = p.id_produto
LEFT JOIN categoria AS c
    ON c.id_categoria = pc.id_categoria
GROUP BY
    p.id_produto,
    p.nome_produto,
    p.descricao,
    p.imagem,
    p.quantidade_estoque;

-- =====================================================
-- 4. CONSULTAS DE TESTE
-- =====================================================

-- SELECT * FROM vw_estoque_por_categoria ORDER BY nome_categoria;
-- SELECT * FROM vw_produtos_estoque ORDER BY nome_produto;

-- SELECT *
-- FROM vw_produtos_estoque
-- WHERE status_estoque IN ('SEM ESTOQUE', 'ESTOQUE CRÍTICO')
-- ORDER BY quantidade_estoque, nome_produto;

-- SELECT
--     status_estoque,
--     COUNT(*) AS total_produtos,
--     SUM(quantidade_estoque) AS estoque_total
-- FROM vw_produtos_estoque
-- GROUP BY status_estoque
-- ORDER BY status_estoque;
