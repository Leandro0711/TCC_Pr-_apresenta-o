<?php
// Modal "Editar perfil": Nome e e-mail (campos editáveis da tabela Usuario).
// Salva em perfil_salvar.php. Em caso de erro, a página volta com o modal já
// aberto (atributo open) mostrando a mensagem.
$perfilErro  = $perfilFlash['erro'] ?? "";
$perfilNome  = $perfilFlash['nome'] ?? $nomeUsuario;
$perfilEmail = $perfilFlash['email'] ?? $emailUsuario;
?>
    <dialog id="modal-perfil" class="modal" closedby="any" aria-labelledby="modal-perfil-titulo" <?php echo $perfilErro ? "open" : ""; ?>>
        <form class="modal-caixa" method="post" action="perfil_salvar.php">
            <div class="modal-topo">
                <button type="button" class="botao botao-texto" commandfor="modal-perfil" command="close">
                    <span aria-hidden="true">←</span> Voltar
                </button>
                <?php logo(); ?>
            </div>

            <div class="modal-corpo">
                <h2 id="modal-perfil-titulo">Editar perfil</h2>
                <p class="subtitulo">Atualize como você aparece no HealthCore.</p>

                <span class="avatar avatar--grande" aria-hidden="true"><?php echo htmlspecialchars($inicialUsuario); ?></span>

                <?php if($perfilErro): ?>
                    <p class="aviso aviso--erro" role="alert"><?php echo htmlspecialchars($perfilErro); ?></p>
                <?php endif; ?>

                <div class="campo">
                    <label for="perfil-nome">Nome de usuário</label>
                    <input class="entrada" type="text" id="perfil-nome" name="nome" maxlength="50" required
                           autocomplete="name" value="<?php echo htmlspecialchars($perfilNome); ?>"
                           aria-describedby="perfil-nome-erro" <?php echo $perfilErro ? "autofocus" : ""; ?>>
                    <p class="campo-erro" id="perfil-nome-erro">Informe seu nome.</p>
                </div>

                <div class="campo">
                    <label for="perfil-email">E-mail</label>
                    <input class="entrada" type="email" id="perfil-email" name="email" maxlength="100" required
                           autocomplete="email" value="<?php echo htmlspecialchars($perfilEmail); ?>"
                           aria-describedby="perfil-email-ajuda perfil-email-erro">
                    <p class="campo-ajuda" id="perfil-email-ajuda">É o e-mail que você usa para entrar.</p>
                    <p class="campo-erro" id="perfil-email-erro">Informe um e-mail válido, por exemplo nome@email.com.</p>
                </div>

                <input type="hidden" name="volta" value="<?php echo $voltaAtual; ?>">
            </div>

            <div class="modal-rodape">
                <button type="submit" class="botao botao-primario">Salvar alterações</button>
            </div>
        </form>
    </dialog>
