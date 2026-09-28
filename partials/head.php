<?php
// Conteúdo do <head> comum a todas as páginas. Defina $tituloPagina antes do include.
// $usaMostrarSenha = true carrega o único JS do projeto (botão "Mostrar" da senha).
?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($tituloPagina ?? "HealthCore"); ?></title>
    <!-- Ícone da aba do navegador (gerado de imagemLogoLadecima.jpeg) -->
    <link rel="icon" type="image/png" sizes="64x64" href="assets/img/favicon.png">
    <link rel="apple-touch-icon" href="assets/img/apple-touch-icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600&family=Nunito+Sans:opsz,wght@6..12,400;6..12,600;6..12,700&display=swap">
    <link rel="stylesheet" href="assets/css/tokens.css">
    <link rel="stylesheet" href="assets/css/app.css">
<?php if(!empty($usaMostrarSenha)): ?>
    <script src="assets/js/mostrar-senha.js" defer></script>
<?php endif; ?>
