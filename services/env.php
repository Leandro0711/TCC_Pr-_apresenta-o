<?php

function carregarEnv($caminho){

    if(!file_exists($caminho)){
        return;
    }

    $linhas = file(
        $caminho,
        FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
    );

    foreach($linhas as $linha){

        $linha = trim($linha);

        if(
            $linha === "" ||
            $linha[0] === "#" ||
            strpos($linha, "=") === false
        ){
            continue;
        }

        [$chave, $valor] = explode("=", $linha, 2);

        $chave = trim($chave);
        $valor = trim($valor, " \t\n\r\0\x0B\"'");

        putenv($chave . "=" . $valor);

        $_ENV[$chave] = $valor;
    }
}

carregarEnv(__DIR__ . "/../.env");