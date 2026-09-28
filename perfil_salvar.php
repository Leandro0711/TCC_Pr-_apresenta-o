<?php
// Recebe o POST do modal "Editar perfil" (Nome e e-mail), valida e volta
// para a página de origem. Não tem tela própria.
require_once("services/conn.php");
require_once("services/UsuarioRepository.php");
require_once("partials/volta.php");
session_start();

if(!isset($_SESSION['id_usuario'])){
    header("Location: login.php");
    exit;
}

$volta = voltaUrl(voltaChaveValida($_POST['volta'] ?? ""), "index.php");

if($_SERVER['REQUEST_METHOD'] !== "POST"){
    header("Location: " . $volta);
    exit;
}

$idUsuario = (int) $_SESSION['id_usuario'];
$nome  = trim($_POST['nome'] ?? "");
$email = trim($_POST['email'] ?? "");
$erro  = "";

if($nome === "" || mb_strlen($nome) > 50){
    $erro = "Informe um nome com até 50 caracteres.";
}elseif(!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 100){
    $erro = "Informe um e-mail válido, por exemplo nome@email.com.";
}else{
    $outroUsuario = usuarioBuscarPorEmail($conn, $email);
    if($outroUsuario && (int) $outroUsuario['id_usuario'] !== $idUsuario){
        $erro = "Este e-mail já está em uso por outra conta.";
    }
}

if($erro === ""){
    try{
        usuarioAtualizar($conn, $idUsuario, $nome, $email);
    }catch(mysqli_sql_exception $e){
        error_log("perfil_salvar: " . $e->getMessage());
        $erro = "Não foi possível salvar agora. Tente novamente em instantes.";
    }
}

if($erro === ""){
    $_SESSION['nome']  = $nome;
    $_SESSION['aviso'] = "Perfil atualizado.";
}else{
    // O header reabre o modal com a mensagem e o que a pessoa digitou
    $_SESSION['perfil_flash'] = ["erro" => $erro, "nome" => $nome, "email" => $email];
}

header("Location: " . $volta);
exit;
