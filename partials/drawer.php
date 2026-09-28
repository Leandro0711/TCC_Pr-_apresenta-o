<?php // Painel de perfil (drawer). Incluído pelo header quando há login. ?>
    <dialog id="drawer-perfil" class="drawer" closedby="any" aria-labelledby="drawer-nome">
        <div class="drawer-conteudo">
            <div class="drawer-topo">
                <button type="button" class="botao botao-icone" commandfor="drawer-perfil" command="close" aria-label="Fechar painel">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true" focusable="false"><path d="M6 6l12 12M18 6L6 18"/></svg>
                </button>
            </div>

            <div class="drawer-perfil">
                <span class="avatar avatar--grande" aria-hidden="true"><?php echo htmlspecialchars($inicialUsuario); ?></span>
                <p class="drawer-nome" id="drawer-nome"><?php echo htmlspecialchars($nomeUsuario); ?></p>
                <p class="drawer-email"><?php echo htmlspecialchars($emailUsuario); ?></p>
            </div>

            <div class="drawer-acoes">
                <!-- O modal abre por cima do painel; "Voltar" fecha o modal e o painel continua ali -->
                <button type="button" class="botao botao-secundario botao--bloco" commandfor="modal-perfil" command="show-modal">Editar perfil</button>
                <a class="botao botao-primario botao--bloco" href="home.php">Treinos e dietas</a>
            </div>

            <div class="drawer-rodape">
                <a class="botao botao-texto" href="logout.php">Sair</a>
                <button type="button" class="botao botao-perigo-texto" commandfor="modal-excluir" command="show-modal">Excluir conta</button>
            </div>
        </div>
    </dialog>
