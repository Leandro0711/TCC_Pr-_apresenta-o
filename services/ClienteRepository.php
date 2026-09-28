<?php
// CRUD da tabela Cliente (perfil de saúde do usuário) e apoio (objetivo)

function clienteBuscarPorUsuario($conn, $idUsuario){
    $stmt = $conn->prepare("SELECT * FROM Cliente WHERE id_usuario = ?");
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Cria o perfil se ainda não existir, ou atualiza os dados quando já existir.
function clienteSalvar($conn, $idUsuario, $dataNasc, $sexo, $altura, $peso, $idObjetivo, $limitacoesFisicas, $idClienteExistente = null){
    if($idClienteExistente){
        $stmt = $conn->prepare("UPDATE Cliente
            SET data_nasc = ?, sexo = ?, altura = ?, peso = ?, id_objetivo = ?, limitacoes_fisicas = ?
            WHERE id_cliente = ?");
        $stmt->bind_param("ssddisi", $dataNasc, $sexo, $altura, $peso, $idObjetivo, $limitacoesFisicas, $idClienteExistente);
        $stmt->execute();
        return $idClienteExistente;
    }

    $stmt = $conn->prepare("INSERT INTO Cliente(id_usuario, data_nasc, sexo, altura, peso, id_objetivo, limitacoes_fisicas)
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issddis", $idUsuario, $dataNasc, $sexo, $altura, $peso, $idObjetivo, $limitacoesFisicas);
    $stmt->execute();
    return $conn->insert_id;
}

function clienteExcluir($conn, $idCliente){
    $stmt = $conn->prepare("DELETE FROM Cliente WHERE id_cliente = ?");
    $stmt->bind_param("i", $idCliente);
    return $stmt->execute();
}

function objetivoListar($conn){
    return $conn->query("SELECT * FROM objetivo ORDER BY id_objetivo");
}

function objetivoBuscarPorId($conn, $idObjetivo){
    $stmt = $conn->prepare("SELECT * FROM objetivo WHERE id_objetivo = ?");
    $stmt->bind_param("i", $idObjetivo);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
