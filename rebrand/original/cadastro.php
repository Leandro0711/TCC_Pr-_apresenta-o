<?php
require_once("services/conn.php");
require_once("services/UsuarioRepository.php");

if(isset($_POST['salvar'])){

    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    if(usuarioCriar($conn, $nome, $email, $senha)){
        header("Location:login.php");
        exit;
    }else{
        $erro = "Erro ao cadastrar!";
    }

}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastro</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="card">
        <h1>Criar conta</h1>

        <?php if(isset($erro)): ?>
            <p class="erro"><?php echo $erro; ?></p>
        <?php endif; ?>

        <form method="post">

            <label>Nome</label>
            <input type="text" name="nome" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Senha</label>
            <input type="password" name="senha" required>

            <input type="submit" name="salvar" value="Cadastrar">

        </form>

        <div class="links">
            Já tem conta? <a href="login.php">Entrar</a>
        </div>
    </div>

</body>
</html>
