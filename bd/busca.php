<?php
    $busca = $_POST["busca"]?? NULL;

    $sqlFilme = "select * from filme where titulo like :busca order by titulo";
    $consulta = $pdo->prepare($sqlFilme);
    $consulta->bindValue(":busca", "$busca");
    $consulta->execute();

    $dadosFilmes = $consulta->fetchAll(PDO::FETCH_OBJ);

    