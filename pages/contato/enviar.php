<?php

$mensagem = $_POST['mensagem'];

$numero = "5544999900604";

$texto = "Olá! Tenho uma mensagem do site: $mensagem";

header("Location: https://wa.me/$numero?text=" . urlencode($texto));
exit;

?> 