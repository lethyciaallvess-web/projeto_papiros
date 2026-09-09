use projeto_papiros;

drop procedure if exists sp_contar_produtos;

create procedure sp_contar_produtos(
    in p_busca varchar(100),
    in p_categoria int
)
select
    count(*) as total_registros
from produto as p
where
    (
        p_busca is null
        or p_busca = ''
        or p.nome_produto like concat('%', p_busca, '%')
    )
    and (
        p_categoria is null
        or p_categoria = 0
        or exists (
            select 1
            from produto_categoria as filtro
            where filtro.id_produto = p.id_produto
                and filtro.id_categoria = p_categoria
        )
    );

drop procedure if exists sp_listar_produtos;

create procedure sp_listar_produtos(
    in p_busca varchar(100),
    in p_categoria int,
    in p_limite int,
    in p_offset int
)
select
    p.id_produto,
    p.nome_produto,
    p.descricao,
    p.imagem,
    p.quantidade_estoque,
    p.valor_unitario,
    round(
        p.quantidade_estoque * p.valor_unitario,
        2
    ) as valor_total,
    fn_classificar_estoque(
        p.quantidade_estoque
    ) as status_estoque,
    min(c.id_categoria) as id_categoria,
    coalesce(
        group_concat(
            distinct c.nome_categoria
            order by c.nome_categoria
            separator ', '
        ),
        'sem categoria'
    ) as nome_categoria
from produto as p
left join produto_categoria as pc
    on pc.id_produto = p.id_produto
left join categoria as c
    on c.id_categoria = pc.id_categoria
where
    (
        p_busca is null
        or p_busca = ''
        or p.nome_produto like concat('%', p_busca, '%')
    )
    and (
        p_categoria is null
        or p_categoria = 0
        or exists (
            select 1
            from    produto_categoria as filtro
            where filtro.id_produto = p.id_produto
                and filtro.id_categoria = p_categoria
        )
    )
group by
    p.id_produto,
    p.nome_produto,
    p.descricao,
    p.imagem,
    p.quantidade_estoque,
    p.valor_unitario
order by p.id_produto
limit p_limite
offset p_offset;