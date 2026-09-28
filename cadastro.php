<?php
require_once("services/conn.php");
require_once("services/UsuarioRepository.php");
require_once("partials/volta.php");
session_start();

// Repassa o fluxo de origem para o login depois do cadastro
$volta = voltaChaveValida($_GET['volta'] ?? $_POST['volta'] ?? "");

if(isset($_POST['salvar'])){

    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    if(usuarioCriar($conn, $nome, $email, $senha)){
        header("Location:login.php" . ($volta ? "?volta=" . $volta : ""));
        exit;
    }else{
        $erro = "Erro ao cadastrar.";
    }

}

$tituloPagina = "Criar conta · HealthCore";
$usaMostrarSenha = true;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<?php include "partials/head.php"; ?>
</head>
<body>

<?php include "partials/header.php"; ?>

    <main id="conteudo" class="pagina pagina--centro">
        <div class="painel">
            <h1>Criar conta</h1>
            <p class="subtitulo">Leva só um minuto. Depois você monta seu plano de treino e dieta.</p>

            <?php if(isset($erro)): ?>
                <p class="aviso aviso--erro" role="alert"><?php echo $erro; ?></p>
            <?php endif; ?>

            <form method="post" action="cadastro.php">
                <input type="hidden" name="volta" value="<?php echo $volta; ?>">

                <div class="campo">
                    <label for="nome">Nome</label>
                    <input class="entrada" type="text" id="nome" name="nome" maxlength="50" autocomplete="name" required
                           aria-describedby="nome-erro">
                    <p class="campo-erro" id="nome-erro">Informe seu nome.</p>
                </div>

                <div class="campo">
                    <label for="email">E-mail</label>
                    <input class="entrada" type="email" id="email" name="email" maxlength="100" autocomplete="email" required
                           aria-describedby="email-erro">
                    <p class="campo-erro" id="email-erro">Informe um e-mail válido, por exemplo nome@email.com.</p>
                </div>

                <div class="campo">
                    <label for="senha">Senha</label>
                    <div class="senha">
                        <input class="entrada" type="password" id="senha" name="senha" autocomplete="new-password" required
                               aria-describedby="senha-erro">
                        <button type="button" class="botao botao-texto" data-mostrar-senha="senha" aria-pressed="false" aria-label="Mostrar senha" hidden>Mostrar</button>
                    </div>
                    <p class="campo-erro" id="senha-erro">Crie uma senha.</p>
                </div>

                <button type="submit" name="salvar" class="botao botao-primario botao--bloco">Cadastrar</button>
            </form>

            <div class="painel-links">
                <p>Já tem conta? <a href="login.php<?php echo $volta ? "?volta=" . $volta : ""; ?>">Entrar</a></p>
            </div>
        </div>
    </main>

<?php include "partials/rodape.php"; ?>

</body>
</html>
