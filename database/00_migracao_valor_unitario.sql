use projeto_papiros;

alter table produto
add column if not exists valor_unitario decimal(10,2)
not null default 0.00
after quantidade_estoque;

update produto as p
left join produto_categoria as pc
    on pc.id_produto = p.id_produto
set p.valor_unitario = case pc.id_categoria
    when 1 then 499.90
    when 2 then 799.90
    when 3 then 1299.90
    when 4 then 949.90
    else 0.00
end
where p.valor_unitario = 0.00;