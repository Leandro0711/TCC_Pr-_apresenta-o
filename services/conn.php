<?php

    $usuario = "localhost";
    $user = "root";
    $pass = "";
    $banco = "HealthCore";

    $conn = new mysqli($usuario, $user, $pass, $banco);

    if($conn->connect_error){
        die("Erro na conexão: " . $conn->connect_error);
    }

    $conn->set_charset("utf8");
?>