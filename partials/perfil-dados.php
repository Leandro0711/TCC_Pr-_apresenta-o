<?php
// Leitura do perfil de saúde usada pelo gerador (movida de home.php sem
// mudança de lógica). Precisa de $conn e $idUsuario definidos antes.
require_once __DIR__ . "/../services/ClienteRepository.php";
require_once __DIR__ . "/../services/ExcecaoRepository.php";

if(!function_exists("chk")){
    function chk($valor, $lista){
        return in_array($valor, $lista) ? "checked" : "";
    }
}

$opcoesAlergias     = ["Leite e derivados", "Ovos", "Amendoim e oleaginosas", "Frutos do mar", "Soja", "Trigo"];
$opcoesIntolerancias = ["Lactose", "Glúten"];
$opcoesCondicoes     = ["Vegetariano", "Vegano", "Diabetes", "Hipertensão", "Doença renal"];
$opcoesFisicas       = ["Joelho", "Coluna/lombar", "Ombro", "Quadril", "Tornozelo/pé", "Dificuldade de mobilidade", "Dificuldade de equilíbrio", "Recuperação pós-lesão"];

$cliente = clienteBuscarPorUsuario($conn, $idUsuario);
$perfilCompleto = $cliente && $cliente['peso'] && $cliente['altura'] && $cliente['id_objetivo'];

// Seleções atuais, usadas para pré-marcar os checkboxes no modo de edição.
$alergiasAtuais = $intolerAtuais = $condicoesAtuais = [];
$fisicasAtuais = [];

if($cliente){
    foreach(excecaoListarPorCliente($conn, $cliente['id_cliente']) as $excecao){
        if($excecao['tipo'] === "Alergia")      $alergiasAtuais[] = $excecao['descricao'];
        if($excecao['tipo'] === "Intolerancia") $intolerAtuais[]  = $excecao['descricao'];
        if($excecao['tipo'] === "Restricao")    $condicoesAtuais[] = $excecao['descricao'];
    }

    if(!empty($cliente['limitacoes_fisicas'])){
        foreach(explode(",", $cliente['limitacoes_fisicas']) as $item){
            $item = trim($item);
            if($item !== ""){
                $fisicasAtuais[] = $item;
            }
        }
    }
}

$objetivos = objetivoListar($conn);

$idadeAtual = "";
if($cliente && $cliente['data_nasc']){
    $idadeAtual = floor((time() - strtotime($cliente['data_nasc'])) / (365.25 * 24 * 3600));
}
