<?php
// Modal do gerador de treino e dieta. Mesmos campos (name) do formulário
// antigo de home.php; o POST continua sendo tratado em home.php.
// Precisa de partials/perfil-dados.php carregado antes.
// $abrirGerador = true: a página já vem com o modal aberto (atributo open).
// Validação sem JS: regras do próprio HTML (required, min, max, step) +
// mensagens fixas por campo, exibidas via CSS (:user-invalid).

function chipsGerador($nome, $opcoes, $marcadas){
    foreach($opcoes as $opcao){
        $valor = htmlspecialchars($opcao);
        echo '<label class="chip"><input type="checkbox" name="' . $nome . '[]" value="' . $valor . '" ' . chk($opcao, $marcadas) . '><span>' . $valor . '</span></label>';
    }
}
?>
    <dialog id="modal-gerador" class="modal modal--grande" closedby="any" aria-labelledby="modal-gerador-titulo" <?php echo !empty($abrirGerador) ? "open" : ""; ?>>
        <form class="modal-caixa" method="post" action="home.php">
            <div class="modal-topo">
                <button type="button" class="botao botao-texto" commandfor="modal-gerador" command="close">Cancelar</button>
                <?php logo(); ?>
            </div>

            <div class="modal-corpo">
                <h2 id="modal-gerador-titulo">Gerar meu treino e dieta</h2>
                <p class="subtitulo">Com estes dados calculamos seu IMC e montamos sugestões para o seu momento.</p>

                <p class="aviso aviso--erro resumo-erros">Revise os campos destacados antes de continuar.</p>

                <div class="grade-campos">
                    <div class="campo">
                        <label for="ger-idade">Idade</label>
                        <input class="entrada" type="number" id="ger-idade" name="idade" min="13" max="120" step="1" inputmode="numeric"
                               value="<?php echo $idadeAtual; ?>" required aria-describedby="ger-idade-erro"
                               <?php echo !empty($abrirGerador) ? "autofocus" : ""; ?>>
                        <p class="campo-erro" id="ger-idade-erro">Informe uma idade entre 13 e 120 anos, em número inteiro.</p>
                    </div>

                    <div class="campo">
                        <label for="ger-sexo">Sexo</label>
                        <select class="entrada" id="ger-sexo" name="sexo" required aria-describedby="ger-sexo-erro">
                            <option value="Masculino" <?php echo (isset($cliente['sexo']) && $cliente['sexo'] == 'Masculino') ? 'selected' : ''; ?>>Masculino</option>
                            <option value="Feminino" <?php echo (isset($cliente['sexo']) && $cliente['sexo'] == 'Feminino') ? 'selected' : ''; ?>>Feminino</option>
                            <option value="Outro" <?php echo (isset($cliente['sexo']) && $cliente['sexo'] == 'Outro') ? 'selected' : ''; ?>>Outro</option>
                        </select>
                        <p class="campo-erro" id="ger-sexo-erro">Escolha uma opção.</p>
                    </div>

                    <div class="campo">
                        <label for="ger-altura">Altura (metros)</label>
                        <input class="entrada" type="number" id="ger-altura" name="altura" step="0.01" min="0.5" max="2.5" inputmode="decimal"
                               placeholder="Ex: 1.75" value="<?php echo $cliente['altura'] ?? ''; ?>" required aria-describedby="ger-altura-erro">
                        <p class="campo-erro" id="ger-altura-erro">Informe a altura em metros, entre 0.50 e 2.50. Exemplo: 1.75.</p>
                    </div>

                    <div class="campo">
                        <label for="ger-peso">Peso (kg)</label>
                        <input class="entrada" type="number" id="ger-peso" name="peso" step="0.01" min="20" max="300" inputmode="decimal"
                               placeholder="Ex: 70.50" value="<?php echo $cliente['peso'] ?? ''; ?>" required aria-describedby="ger-peso-erro">
                        <p class="campo-erro" id="ger-peso-erro">Informe o peso em quilos, entre 20 e 300. Exemplo: 70.50.</p>
                    </div>

                    <div class="campo campo--inteiro">
                        <label for="ger-objetivo">Objetivo</label>
                        <select class="entrada" id="ger-objetivo" name="id_objetivo" required aria-describedby="ger-objetivo-erro">
                            <?php while($obj = $objetivos->fetch_assoc()): ?>
                                <option value="<?php echo $obj['id_objetivo']; ?>" <?php echo (isset($cliente['id_objetivo']) && $cliente['id_objetivo'] == $obj['id_objetivo']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($obj['nome']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <p class="campo-erro" id="ger-objetivo-erro">Escolha um objetivo.</p>
                    </div>
                </div>

                <fieldset class="grupo">
                    <legend>Restrições alimentares (opcional)</legend>

                    <span class="grupo-titulo">Alergias</span>
                    <div class="chips"><?php chipsGerador("alergias", $opcoesAlergias, $alergiasAtuais); ?></div>

                    <span class="grupo-titulo">Intolerâncias</span>
                    <div class="chips"><?php chipsGerador("intolerancias", $opcoesIntolerancias, $intolerAtuais); ?></div>

                    <span class="grupo-titulo">Preferenciais / Condições</span>
                    <div class="chips"><?php chipsGerador("condicoes", $opcoesCondicoes, $condicoesAtuais); ?></div>
                </fieldset>

                <fieldset class="grupo">
                    <legend>Limitações físicas (opcional)</legend>
                    <div class="chips"><?php chipsGerador("fisicas", $opcoesFisicas, $fisicasAtuais); ?></div>
                </fieldset>
            </div>

            <div class="modal-rodape">
                <button type="submit" name="salvar_perfil" class="botao botao-primario">Concluir</button>
            </div>
        </form>
    </dialog>
