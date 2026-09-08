use projeto_papiros;

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
    fn_classificar_estoque(
        p.quantidade_estoque
    ) as status_estoque,
    c.id_categoria,
    c.nome_categoria
from produto as p
inner join produto_categoria as pc
    on pc.id_produto = p.id_produto
inner join categoria as c
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
        or c.id_categoria = p_categoria
    )
order by p.id_produto
limit p_limite
offset p_offset;