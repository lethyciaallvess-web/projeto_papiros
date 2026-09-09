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

create or replace view vw_estoque_por_categoria as
select
    c.id_categoria,
    c.nome_categoria,
    count(distinct p.id_produto) as total_produtos,
    coalesce(sum(p.quantidade_estoque), 0) as estoque_total,
    round(coalesce(avg(p.quantidade_estoque), 0), 2) as media_estoque,
    round(
        coalesce(
            sum(p.quantidade_estoque * p.valor_unitario),
            0
        ),
        2
    ) as valor_total_estoque,
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
    c.nome_categoria;

create or replace view vw_produtos_categorias as
select
    p.id_produto,
    p.nome_produto,
    p.descricao,
    p.imagem,
    p.quantidade_estoque,
    p.valor_unitario,
    c.id_categoria,
    c.nome_categoria
from produto as p
left join produto_categoria as pc
    on pc.id_produto = p.id_produto
left join categoria as c
    on c.id_categoria = pc.id_categoria;

create or replace view vw_produtos_estoque as
select
    p.id_produto,
    p.nome_produto,
    p.descricao,
    p.imagem,
    p.quantidade_estoque,
    p.valor_unitario,
    p.quantidade_estoque * p.valor_unitario as valor_total,
    case
        when p.quantidade_estoque = 0 then 'sem estoque'
        when p.quantidade_estoque between 1 and 5
            then 'estoque crítico'
        else 'estoque normal'
    end as status_estoque,
    count(distinct c.id_categoria) as total_categorias,
    coalesce(
        group_concat(
            distinct c.nome_categoria
            order by c.nome_categoria
            separator ', '
        ),
        'sem categoria'
    ) as categorias
from produto as p
left join produto_categoria as pc
    on pc.id_produto = p.id_produto
left join categoria as c
    on c.id_categoria = pc.id_categoria
group by
    p.id_produto,
    p.nome_produto,
    p.descricao,
    p.imagem,
    p.quantidade_estoque,
    p.valor_unitario;