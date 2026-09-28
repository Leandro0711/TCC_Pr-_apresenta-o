<?php
// CRUD da tabela Usuario (cadastro, login, recuperação de senha, exclusão)

function usuarioCriar($conn, $nome, $email, $senhaHash){
    $stmt = $conn->prepare("INSERT INTO Usuario(Nome, email, senha) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nome, $email, $senhaHash);
    return $stmt->execute() ? $conn->insert_id : false;
}

function usuarioBuscarPorEmail($conn, $email){
    $stmt = $conn->prepare("SELECT * FROM Usuario WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function usuarioBuscarPorId($conn, $idUsuario){
    $stmt = $conn->prepare("SELECT * FROM Usuario WHERE id_usuario = ?");
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function usuarioAtualizar($conn, $idUsuario, $nome, $email){
    $stmt = $conn->prepare("UPDATE Usuario SET Nome = ?, email = ? WHERE id_usuario = ?");
    $stmt->bind_param("ssi", $nome, $email, $idUsuario);
    return $stmt->execute();
}

function usuarioAtualizarSenha($conn, $email, $novaSenhaHash){
    $stmt = $conn->prepare("UPDATE Usuario SET senha = ? WHERE email = ?");
    $stmt->bind_param("ss", $novaSenhaHash, $email);
    return $stmt->execute();
}

function usuarioExcluir($conn, $idUsuario){
    $stmt = $conn->prepare("DELETE FROM Usuario WHERE id_usuario = ?");
    $stmt->bind_param("i", $idUsuario);
    return $stmt->execute();
}
