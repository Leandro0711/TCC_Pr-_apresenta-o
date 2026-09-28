<?php

require_once("services/conn.php");
require_once("services/UsuarioRepository.php");
require_once("services/env.php");
require_once("services/PHPMailer/Exception.php");
require_once("services/PHPMailer/PHPMailer.php");
require_once("services/PHPMailer/SMTP.php");

session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

function enviarEmail($emailDestinatario, $codigoVerificacao){
    $gmailUser = getenv("GMAIL_USER");
    $gmailSenha = getenv("GMAIL_APP_PASSWORD");

    if(empty($gmailUser)){
        return "GMAIL_USER não foi encontrado no arquivo .env.";
    }

    if(empty($gmailSenha)){
        return "GMAIL_APP_PASSWORD não foi encontrado no arquivo .env.";
    }

    $mail = new PHPMailer(true);

    try{
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = $gmailUser;
        $mail->Password = $gmailSenha;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = "UTF-8";

        $mail->setFrom($gmailUser, "Suporte HealthCore");
        $mail->addAddress($emailDestinatario);

        $mail->isHTML(true);
        $mail->Subject = "Código de Recuperação de Senha";

        $mail->Body = "
            <div style='font-family: Arial, sans-serif;'>
                <h2>Recuperação de senha - HealthCore</h2>
                <p>Olá!</p>
                <p>Recebemos uma solicitação para redefinir sua senha.</p>
                <p>Seu código de recuperação é:</p>
                <h1 style='letter-spacing: 5px;'>{$codigoVerificacao}</h1>
                <p>Digite esse código na página de recuperação de senha.</p>
                <p>Se você não solicitou a recuperação da senha, ignore este e-mail.</p>
            </div>
        ";

        $mail->AltBody = "Seu código de recuperação de senha do HealthCore é: " . $codigoVerificacao;

        $mail->send();

        return true;

    }catch(PHPMailerException $e){
        error_log("PHPMailer Exception: " . $e->getMessage());
        error_log("PHPMailer ErrorInfo: " . $mail->ErrorInfo);

        return "PHPMailer Exception: " . $e->getMessage() . " | ErrorInfo: " . $mail->ErrorInfo;
    }
}

$etapa = 1;
$erro = "";
$email = "";

if(isset($_POST["buscar"])){
    $email = trim($_POST["email"]);

    $stmt = $conn->prepare("SELECT * FROM Usuario WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if($resultado && $resultado->num_rows > 0){
        $codigo = rand(100000, 999999);

        $_SESSION["codigo_recuperacao"] = $codigo;
        $_SESSION["email_recuperacao"] = $email;
        $_SESSION["codigo_confirmado"] = false;

        $resultadoEmail = enviarEmail($email, $codigo);

        if($resultadoEmail === true){
            $etapa = 2;
        }else{
            $erro = "Erro ao enviar o e-mail:<br><br>" . htmlspecialchars($resultadoEmail);
        }
    }else{
        $erro = "E-mail não encontrado.";
    }
}

if(isset($_POST["verificar"])){
    $email = $_SESSION["email_recuperacao"] ?? "";
    $codigoDigitado = trim($_POST["codigo"] ?? "");

    if(isset($_SESSION["codigo_recuperacao"]) && !empty($email) && $codigoDigitado == $_SESSION["codigo_recuperacao"]){
        $_SESSION["codigo_confirmado"] = true;
        $etapa = 3;
    }else{
        $erro = "Código inválido.";
        $etapa = 2;
    }
}

if(isset($_POST["redefinir"])){
    $email = $_SESSION["email_recuperacao"] ?? "";

    if(!isset($_SESSION["codigo_confirmado"]) || $_SESSION["codigo_confirmado"] !== true || empty($email)){
        $erro = "Sessão inválida. Refaça o processo.";
        $etapa = 1;
    }else{
        $novaSenha = $_POST["nova_senha"] ?? "";

        if(empty($novaSenha)){
            $erro = "Digite uma nova senha.";
            $etapa = 3;
        }else{
            $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("UPDATE Usuario SET senha = ? WHERE email = ?");
            $stmt->bind_param("ss", $senhaHash, $email);

            if($stmt->execute()){
                unset($_SESSION["codigo_recuperacao"]);
                unset($_SESSION["email_recuperacao"]);
                unset($_SESSION["codigo_confirmado"]);

                header("Location: index.php");
                exit;
            }else{
                $erro = "Erro ao redefinir a senha.";
                $etapa = 3;
            }
        }
    }
}

if(!isset($_POST["buscar"]) && !isset($_POST["verificar"]) && !isset($_POST["redefinir"])){
    if(isset($_SESSION["codigo_confirmado"]) && $_SESSION["codigo_confirmado"] === true){
        $etapa = 3;
        $email = $_SESSION["email_recuperacao"];
    }elseif(isset($_SESSION["email_recuperacao"])){
        $etapa = 2;
        $email = $_SESSION["email_recuperacao"];
    }
}
?>
<?php $tituloPagina = "Recuperar senha · HealthCore"; $usaMostrarSenha = true; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<?php include "partials/head.php"; ?>
</head>
<body>

<?php include "partials/header.php"; ?>

    <main id="conteudo" class="pagina pagina--centro">
        <div class="painel">
            <h1>Recuperar senha</h1>

            <?php if($erro != ""): ?>
                <p class="aviso aviso--erro" role="alert"><?php echo $erro; ?></p>
            <?php endif; ?>

            <?php if($etapa == 1): ?>

                <p class="subtitulo">Vamos enviar um código de 6 dígitos para o seu e-mail.</p>

                <form method="post">
                    <div class="campo">
                        <label for="email">Informe seu e-mail cadastrado</label>
                        <input class="entrada" type="email" id="email" name="email" autocomplete="email" required
                               value="<?php echo htmlspecialchars($email); ?>" aria-describedby="email-erro">
                        <p class="campo-erro" id="email-erro">Informe um e-mail válido, por exemplo nome@email.com.</p>
                    </div>
                    <button type="submit" name="buscar" class="botao botao-primario botao--bloco">Enviar código</button>
                </form>

            <?php elseif($etapa == 2): ?>

                <p class="subtitulo">
                    Enviamos um código de 6 dígitos para
                    <strong><?php echo htmlspecialchars($email); ?></strong>.
                </p>

                <form method="post">
                    <div class="campo">
                        <label for="codigo">Código de verificação</label>
                        <input class="entrada" type="text" id="codigo" name="codigo" maxlength="6" inputmode="numeric"
                               autocomplete="one-time-code" required aria-describedby="codigo-erro">
                        <p class="campo-erro" id="codigo-erro">Digite o código que chegou no seu e-mail.</p>
                    </div>
                    <button type="submit" name="verificar" class="botao botao-primario botao--bloco">Confirmar código</button>
                </form>

                <div class="painel-links">
                    <a href="esqueci_senha.php">Não recebeu? Solicitar novamente</a>
                </div>

            <?php else: ?>

                <p class="subtitulo">Escolha uma nova senha para a sua conta.</p>

                <form method="post">
                    <div class="campo">
                        <label for="nova_senha">Nova senha</label>
                        <div class="senha">
                            <input class="entrada" type="password" id="nova_senha" name="nova_senha" autocomplete="new-password" required
                                   aria-describedby="nova_senha-erro">
                            <button type="button" class="botao botao-texto" data-mostrar-senha="nova_senha" aria-pressed="false" aria-label="Mostrar senha" hidden>Mostrar</button>
                        </div>
                        <p class="campo-erro" id="nova_senha-erro">Digite uma nova senha.</p>
                    </div>
                    <button type="submit" name="redefinir" class="botao botao-primario botao--bloco">Redefinir senha</button>
                </form>

            <?php endif; ?>

            <div class="painel-links">
                <a href="login.php">Voltar para o login</a>
            </div>
        </div>
    </main>

<?php include "partials/rodape.php"; ?>

</body>
</html>
