<?php
// CRUD da tabela excecao_alimentar (alergias, intolerâncias e restrições)

function excecaoExcluirPorCliente($conn, $idCliente){
    $stmt = $conn->prepare("DELETE FROM excecao_alimentar WHERE id_cliente = ?");
    $stmt->bind_param("i", $idCliente);
    return $stmt->execute();
}

function excecaoInserir($conn, $idCliente, $tipo, $descricao, $observacao = null){
    $stmt = $conn->prepare("INSERT INTO excecao_alimentar(id_cliente, tipo, descricao, observacao)
        VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $idCliente, $tipo, $descricao, $observacao);
    return $stmt->execute();
}

// Recebe os itens marcados nos três grupos de checkboxes do formulário,
// substituindo qualquer seleção anterior.
function excecaoSalvarSelecionadas($conn, $idCliente, array $alergias, array $intolerancias, array $condicoes){
    excecaoExcluirPorCliente($conn, $idCliente);

    foreach($alergias as $item){
        excecaoInserir($conn, $idCliente, "Alergia", $item);
    }
    foreach($intolerancias as $item){
        excecaoInserir($conn, $idCliente, "Intolerancia", $item);
    }
    foreach($condicoes as $item){
        excecaoInserir($conn, $idCliente, "Restricao", $item);
    }
}

function excecaoListarPorCliente($conn, $idCliente){
    $stmt = $conn->prepare("SELECT * FROM excecao_alimentar WHERE id_cliente = ?");
    $stmt->bind_param("i", $idCliente);
    $stmt->execute();
    $itens = [];
    $resultado = $stmt->get_result();
    while($linha = $resultado->fetch_assoc()){
        $itens[] = $linha;
    }
    return $itens;
}
