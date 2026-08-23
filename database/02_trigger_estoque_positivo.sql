use projeto_papiros;

drop trigger if exists trg_produto_estoque_positivo;

create trigger trg_produto_estoque_positivo
before update on produto
for each row
set new.quantidade_estoque = abs(new.quantidade_estoque);