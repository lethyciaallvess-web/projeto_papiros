use projeto_papiros;

-- analise de estoque por categoria

with estoque_por_categoria as (
    select
        c.id_categoria,
        c.nome_categoria,
        count(distinct p.id_produto) as total_produtos,
        coalesce(sum(p.quantidade_estoque), 0) as estoque_total,
        coalesce(avg(p.quantidade_estoque), 0) as media_estoque,
        count(distinct case
            when p.quantidade_estoque = 0 then p.id_produto
        end) as produtos_sem_estoque,
        count(distinct case
            when p.quantidade_estoque between 1 and 5 then p.id_produto
        end) as produtos_estoque_critico,
        count(distinct case
            when p.quantidade_estoque > 5 then p.id_produto
        end) as produtos_estoque_normal
    from categoria as c
    left join produto_categoria as pc
        on pc.id_categoria = c.id_categoria
    left join produto as p
        on p.id_produto = pc.id_produto
    group by
        c.id_categoria,
        c.nome_categoria
)
select
    id_categoria,
    nome_categoria,
    total_produtos,
    estoque_total,
    round(media_estoque, 2) as media_estoque,
    produtos_sem_estoque,
    produtos_estoque_critico,
    produtos_estoque_normal
from estoque_por_categoria
order by nome_categoria;