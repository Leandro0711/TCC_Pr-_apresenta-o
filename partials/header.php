<?php
// Header global (todas as páginas). Precisa de session_start() antes.
// Logado: ícone com a inicial abre o painel de perfil (drawer).
// Visitante: ícone leva ao login.
// Sem JavaScript: drawer e modais abrem com commandfor/command (HTML nativo)
// e fecham com Esc, clique fora (closedby="any") ou command="close".

require_once __DIR__ . "/logo.php";
require_once __DIR__ . "/volta.php";

$logado = isset($_SESSION['id_usuario']);

if($logado){
    require_once __DIR__ . "/../services/conn.php";
    require_once __DIR__ . "/../services/UsuarioRepository.php";

    $usuarioTopo  = usuarioBuscarPorId($conn, $_SESSION['id_usuario']);
    $nomeUsuario  = $usuarioTopo['Nome'] ?? ($_SESSION['nome'] ?? "");
    $emailUsuario = $usuarioTopo['email'] ?? "";
    $inicialUsuario = mb_strtoupper(mb_substr(trim($nomeUsuario), 0, 1)) ?: "?";
}

// Para onde voltar depois de salvar o perfil (lista fixa em volta.php)
$voltaAtual = (basename($_SERVER['PHP_SELF']) === "home.php") ? "resultado" : "inicio";

// Mensagens de uma só exibição (gravadas por perfil_salvar.php)
$avisoTopo   = $_SESSION['aviso'] ?? null;
$perfilFlash = $_SESSION['perfil_flash'] ?? null;
unset($_SESSION['aviso'], $_SESSION['perfil_flash']);
?>
    <a class="pular-conteudo" href="#conteudo">Pular para o conteúdo</a>

    <header class="topo">
        <div class="topo-conteudo">
            <a class="marca" href="index.php" aria-label="HealthCore, página inicial"><?php logo(); ?></a>

            <?php if($logado): ?>
                <!-- Abre o painel sem JS (Invoker Commands). Clicar de novo no ícone cai
                     no véu do painel, que fecha por closedby="any". -->
                <button type="button" class="avatar" commandfor="drawer-perfil" command="show-modal"
                        aria-controls="drawer-perfil" aria-haspopup="dialog">
                    <span aria-hidden="true"><?php echo htmlspecialchars($inicialUsuario); ?></span>
                    <span class="visualmente-oculto">Abrir painel de perfil</span>
                </button>
            <?php else: ?>
                <a class="avatar avatar--visitante" href="login.php?volta=inicio">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true" focusable="false"><circle cx="12" cy="8" r="4"/><path d="M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6"/></svg>
                    <span class="visualmente-oculto">Entrar</span>
                </a>
            <?php endif; ?>
        </div>
    </header>

    <?php if($avisoTopo): ?>
        <div class="aviso-topo">
            <p class="aviso aviso--sucesso" role="status"><?php echo htmlspecialchars($avisoTopo); ?></p>
        </div>
    <?php endif; ?>

    <?php
    if($logado){
        include __DIR__ . "/drawer.php";
        include __DIR__ . "/modal-perfil.php";
        include __DIR__ . "/modal-excluir.php";
    }
    ?>
