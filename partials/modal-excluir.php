<?php // Confirmação de exclusão. Envia para o excluir_conta.php existente (confere a senha). ?>
    <dialog id="modal-excluir" class="modal" closedby="any" aria-labelledby="modal-excluir-titulo">
        <form class="modal-caixa" method="post" action="excluir_conta.php">
            <div class="modal-corpo">
                <h2 id="modal-excluir-titulo">Excluir conta</h2>
                <p class="subtitulo">Essa ação é permanente e vai apagar todos os seus dados do sistema (perfil, histórico de IMC, restrições alimentares, etc).</p>

                <div class="campo">
                    <label for="excluir-senha">Confirme sua senha para continuar</label>
                    <input class="entrada" type="password" id="excluir-senha" name="senha" required
                           autocomplete="current-password" aria-describedby="excluir-senha-erro">
                    <p class="campo-erro" id="excluir-senha-erro">Digite sua senha para confirmar.</p>
                </div>
            </div>

            <div class="modal-rodape">
                <button type="button" class="botao botao-secundario" commandfor="modal-excluir" command="close">Cancelar</button>
                <button type="submit" name="excluir" class="botao botao-perigo">Excluir minha conta</button>
            </div>
        </form>
    </dialog>
