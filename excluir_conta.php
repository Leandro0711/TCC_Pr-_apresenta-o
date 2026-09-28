<?php
require_once("services/conn.php");
require_once("services/UsuarioRepository.php");
session_start();

if(!isset($_SESSION['id_usuario'])){
    header("Location:login.php");
    exit;
}

$idUsuario = $_SESSION['id_usuario'];
$erro = "";

if(isset($_POST['excluir'])){

    $senha = $_POST['senha'];
    $usuario = usuarioBuscarPorId($conn, $idUsuario);

    if($usuario && password_verify($senha, $usuario['senha'])){

        usuarioExcluir($conn, $idUsuario);

        session_destroy();
        header("Location:login.php");
        exit;

    }else{
        $erro = "Senha incorreta.";
    }

}
?>
<?php $tituloPagina = "Excluir conta · HealthCore"; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<?php include "partials/head.php"; ?>
</head>
<body>

<?php include "partials/header.php"; ?>

    <main id="conteudo" class="pagina pagina--centro">
        <div class="painel">
            <h1>Excluir conta</h1>
            <p class="subtitulo">Essa ação é permanente e vai apagar todos os seus dados do sistema (perfil, histórico de IMC, restrições alimentares, etc).</p>

            <?php if($erro != ""): ?>
                <p class="aviso aviso--erro" role="alert"><?php echo $erro; ?></p>
            <?php endif; ?>

            <form method="post">
                <div class="campo">
                    <label for="senha">Confirme sua senha para continuar</label>
                    <input class="entrada" type="password" id="senha" name="senha" autocomplete="current-password" required
                           aria-describedby="senha-erro">
                    <p class="campo-erro" id="senha-erro">Digite sua senha para confirmar.</p>
                </div>
                <button type="submit" name="excluir" class="botao botao-perigo botao--bloco">Excluir minha conta</button>
            </form>

            <div class="painel-links">
                <a href="home.php">Cancelar</a>
            </div>
        </div>
    </main>

<?php include "partials/rodape.php"; ?>

</body>
</html>
