<?php
require_once("services/conn.php");
require_once("services/UsuarioRepository.php");
require_once("partials/volta.php");
session_start();

$erro = "";

// Fluxo que a pessoa tentou acessar antes do login (lista fixa em partials/volta.php)
$volta = voltaChaveValida($_GET['volta'] ?? $_POST['volta'] ?? "");

if(isset($_POST['entrar'])){

    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $usuario = usuarioBuscarPorEmail($conn, $email);

    if($usuario && password_verify($senha, $usuario['senha'])){

        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['nome'] = $usuario['Nome'];

        header("Location: " . voltaUrl($volta));
        exit;

    }else{

        $erro = "E-mail ou senha inválidos.";

    }

}

$tituloPagina = "Entrar · HealthCore";
$usaMostrarSenha = true;
$linkCadastro = "cadastro.php" . ($volta ? "?volta=" . $volta : "");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<?php include "partials/head.php"; ?>
</head>
<body>

<?php include "partials/header.php"; ?>

    <main id="conteudo" class="pagina pagina--centro">
        <div class="painel">
            <h1>Entrar</h1>
            <p class="subtitulo">Seu assistente de saúde, dieta e treino.</p>

            <?php if($erro != ""): ?>
                <p class="aviso aviso--erro" role="alert"><?php echo $erro; ?></p>
            <?php endif; ?>

            <form method="post" action="login.php">
                <input type="hidden" name="volta" value="<?php echo $volta; ?>">

                <div class="campo">
                    <label for="email">E-mail</label>
                    <input class="entrada" type="email" id="email" name="email" placeholder="nome@email.com"
                           autocomplete="email" required aria-describedby="email-erro"
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ""); ?>">
                    <p class="campo-erro" id="email-erro">Informe um e-mail válido, por exemplo nome@email.com.</p>
                </div>

                <div class="campo">
                    <label for="senha">Senha</label>
                    <div class="senha">
                        <input class="entrada" type="password" id="senha" name="senha"
                               autocomplete="current-password" required aria-describedby="senha-erro">
                        <button type="button" class="botao botao-texto" data-mostrar-senha="senha" aria-pressed="false" aria-label="Mostrar senha" hidden>Mostrar</button>
                    </div>
                    <p class="campo-erro" id="senha-erro">Informe sua senha.</p>
                </div>

                <button type="submit" name="entrar" class="botao botao-primario botao--bloco">Entrar</button>
            </form>

            <div class="painel-links">
                <a href="esqueci_senha.php">Esqueci minha senha</a>
                <p>Não possui conta? <a href="<?php echo $linkCadastro; ?>">Cadastre-se</a></p>
            </div>
        </div>
    </main>

<?php include "partials/rodape.php"; ?>

</body>
</html>
