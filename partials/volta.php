<?php
// Parâmetro "volta": depois do login, leva o usuário de volta ao fluxo que
// ele tentou acessar. Lista FIXA de destinos — nunca redireciona para uma URL
// vinda da requisição (evita redirecionamento aberto).

function voltaDestinos(){
    return [
        "inicio"    => "index.php",
        "gerador"   => "index.php?gerador=1",
        "resultado" => "home.php",
    ];
}

// Devolve a chave se ela for conhecida; senão, string vazia.
function voltaChaveValida($chave){
    return (is_string($chave) && array_key_exists($chave, voltaDestinos())) ? $chave : "";
}

function voltaUrl($chave, $padrao = "home.php"){
    $destinos = voltaDestinos();
    return $destinos[$chave] ?? $padrao;
}
