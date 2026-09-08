use projeto_papiros;
drop function if exists fn_classificar_estoque;

create function fn_classificar_estoque(
    p_quantidade int
)
returns varchar(30)
deterministic
return case
    when coalesce(abs(p_quantidade), 0) = 0
        then 'SEM ESTOQUE'
    when coalesce(abs(p_quantidade), 0) between 1 and 5
        then 'ESTOQUE CRÍTICO'
    else 'ESTOQUE NORMAL'
end;