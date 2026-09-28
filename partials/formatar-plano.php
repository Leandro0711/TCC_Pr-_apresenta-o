<?php
// Converte a notação do catálogo de treinos em texto corrido, mantendo as faixas:
//   "2-3 x 8-12"        -> "2 a 3 séries de 8 a 12 repetições"
//   "2 x 15-20 s"       -> "2 séries de 15 a 20 segundos"
//   "2 x 8 por lado"    -> "2 séries de 8 repetições por lado"
//   "5-8 min"           -> "5 a 8 min"
//   "2 x 15-30 s / 8 por lado" -> "... segundos ou 8 repetições por lado"
// Formato desconhecido: devolve o texto original, só trocando "-" por " a ".

function formatarFaixa($inicio, $fim){
    return ($fim !== "" && $fim !== null) ? "$inicio a $fim" : $inicio;
}

function formatarQuantidade($qtd, $qtdFim, $segundos, $porLado){
    $unidade = $segundos ? "segundos" : "repetições";
    return formatarFaixa($qtd, $qtdFim) . " " . $unidade . ($porLado ? " por lado" : "");
}

function formatarParteDose($parte){
    $parte = trim($parte);

    if(preg_match('/^(\d+)(?:-(\d+))? min$/', $parte, $m)){
        return formatarFaixa($m[1], $m[2] ?? "") . " min";
    }

    if(preg_match('/^(\d+)(?:-(\d+))? x (\d+)(?:-(\d+))?( s)?( por lado)?$/', $parte, $m)){
        $series = formatarFaixa($m[1], $m[2]);
        $rotuloSeries = ($m[1] === "1" && $m[2] === "") ? "série" : "séries";
        return "$series $rotuloSeries de " . formatarQuantidade($m[3], $m[4] ?? "", !empty($m[5]), !empty($m[6]));
    }

    // Segunda parte de "... / 8 por lado" (sem número de séries)
    if(preg_match('/^(\d+)(?:-(\d+))?( s)?( por lado)?$/', $parte, $m)){
        return formatarQuantidade($m[1], $m[2] ?? "", !empty($m[3]), !empty($m[4]));
    }

    return str_replace("-", " a ", $parte);
}

function formatarDose($dose){
    return implode(" ou ", array_map("formatarParteDose", explode(" / ", $dose)));
}
