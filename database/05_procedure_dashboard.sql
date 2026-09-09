use projeto_papiros;

drop procedure if exists sp_dashboard_indicadores;

create procedure sp_dashboard_indicadores()
select
    id_categoria,
    nome_categoria,
    total_produtos,
    estoque_total,
    media_estoque,
    valor_total_estoque,
    produtos_sem_estoque,
    produtos_estoque_critico,
    produtos_estoque_normal
from vw_estoque_por_categoria
order by nome_categoria;

drop procedure if exists sp_dashboard_inventario;

create procedure sp_dashboard_inventario()
select
    id_produto,
    quantidade_estoque,
    valor_unitario,
    round(
        quantidade_estoque * valor_unitario,
        2
    ) as valor_total,
    fn_classificar_estoque(
        quantidade_estoque
    ) as status_estoque
from produto
order by id_produto;