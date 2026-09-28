<?php
// Cálculo de IMC, classificação por faixa etária e histórico (tabela imc)

function imcCalcular($peso, $altura){
    if($altura <= 0) return 0;
    return round($peso / ($altura * $altura), 2);
}

// Adulto e Idoso: faixas numéricas cadastradas na tabela classificacao_imc.
function imcClassificarPorTabela($conn, $imc, $faixaEtaria){
    $stmt = $conn->prepare("SELECT * FROM classificacao_imc
        WHERE faixa_etaria = ? AND ? BETWEEN imc_min AND imc_max
        LIMIT 1");
    $stmt->bind_param("sd", $faixaEtaria, $imc);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Adolescente (13-18): a tabela classificacao_imc só cobre faixas numéricas
// fixas (Adulto/Idoso). A OMS recomenda comparar o IMC com uma referência
// por idade e sexo biológico, então usamos uma curva aproximada aqui.
// Valores didáticos, obtidos por interpolação entre as faixas de 13 e 19
// anos (nesta última, a curva da OMS converge para os cortes de adulto:
// 18,5 / 25 / 30). Para uso real, substituir pelos pontos de corte
// oficiais OMS/SISVAN por idade e sexo (ver documento de planejamento).
function imcReferenciaAdolescente($idade){
    $tabela = [
        13 => [15.5, 21.0, 24.5],
        14 => [15.8, 21.8, 25.5],
        15 => [16.1, 22.5, 26.5],
        16 => [16.5, 23.2, 27.5],
        17 => [16.8, 23.8, 28.5],
        18 => [17.0, 24.5, 29.5],
    ];
    $idade = max(13, min(18, (int) $idade));
    return $tabela[$idade];
}

function imcClassificarAdolescente($imc, $idade){
    [$limiteMagreza, $limiteAdequado, $limiteSobrepeso] = imcReferenciaAdolescente($idade);

    if($imc < $limiteMagreza)   return "Magreza";
    if($imc < $limiteAdequado)  return "IMC adequado";
    if($imc < $limiteSobrepeso) return "Sobrepeso";
    return "Obesidade";
}

function imcRegistrar($conn, $idCliente, $valorImc, $idClassificacao){
    $stmt = $conn->prepare("INSERT INTO imc(id_cliente, valor_imc, id_classificacao, data_calculo)
        VALUES (?, ?, ?, CURDATE())");
    $stmt->bind_param("idi", $idCliente, $valorImc, $idClassificacao);
    return $stmt->execute();
}

function imcBuscarUltimo($conn, $idCliente){
    // LEFT JOIN: adolescentes são gravados com id_classificacao = NULL,
    // já que a tabela classificacao_imc só cobre faixas de Adulto/Idoso.
    $stmt = $conn->prepare("SELECT imc.valor_imc, imc.data_calculo, classificacao_imc.classificacao
        FROM imc
        LEFT JOIN classificacao_imc ON imc.id_classificacao = classificacao_imc.id_classificacao
        WHERE imc.id_cliente = ?
        ORDER BY imc.data_calculo DESC, imc.id_imc DESC
        LIMIT 1");
    $stmt->bind_param("i", $idCliente);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
