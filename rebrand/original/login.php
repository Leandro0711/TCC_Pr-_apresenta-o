<?php
require_once("services/conn.php");
require_once("services/UsuarioRepository.php");
session_start();

$erro = "";

if(isset($_POST['entrar'])){

    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $usuario = usuarioBuscarPorEmail($conn, $email);

    if($usuario && password_verify($senha, $usuario['senha'])){

        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['nome'] = $usuario['Nome'];

        header("Location: home.php");
        exit;

    }else{

        $erro = "E-mail ou senha inválidos.";

    }

}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Health Core</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Material+Symbols+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="card">
        <div class="logo">💚</div>
        <h1>HealthCore</h1>
        <p class="subtitulo">Seu assistente inteligente de saúde, dieta e treino.</p>

        <?php if($erro != ""): ?>
            <p class="erro"><?php echo $erro; ?></p>
        <?php endif; ?>

        <form method="post">
            <label>E-mail</label>
            <div class="campo">
                <span class="material-symbols-outlined">mail</span>
                <input type="email" name="email" placeholder="Digite seu e-mail" required>
            </div>

            <label>Senha</label>
            <div class="campo">
                <span class="material-symbols-outlined">lock</span>
                <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>
                <span class="material-symbols-outlined mostrar" onclick="mostrarSenha()">visibility</span>
            </div>

            <input type="submit" name="entrar" value="Entrar">
        </form>

        <div class="links">
            <a href="esqueci_senha.php">Esqueci minha senha</a>
            <br><br>
            Não possui conta? <a href="cadastro.php">Cadastre-se</a>
        </div>

        <footer>Health Core © 2026</footer>
    </div>

    <script>
        function mostrarSenha(){
            let senha = document.getElementById("senha");
            senha.type = (senha.type === "password") ? "text" : "password";
        }
    </script>

</body>
</html>
