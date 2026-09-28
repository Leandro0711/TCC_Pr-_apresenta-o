<?php
require_once("services/conn.php");
require_once("services/ClienteRepository.php");
require_once("services/ImcRepository.php");
require_once("services/ExcecaoRepository.php");
require_once("services/GeradorService.php");
session_start();

if(!isset($_SESSION['id_usuario'])){
    header("Location: login.php");
    exit;
}

$idUsuario = $_SESSION['id_usuario'];

function chk($valor, $lista){
    return in_array($valor, $lista) ? "checked" : "";
}

$opcoesAlergias     = ["Leite e derivados", "Ovos", "Amendoim e oleaginosas", "Frutos do mar", "Soja", "Trigo"];
$opcoesIntolerancias = ["Lactose", "Glúten"];
$opcoesCondicoes     = ["Vegetariano", "Vegano", "Diabetes", "Hipertensão", "Doença renal"];
$opcoesFisicas       = ["Joelho", "Coluna/lombar", "Ombro", "Quadril", "Tornozelo/pé", "Dificuldade de mobilidade", "Dificuldade de equilíbrio", "Recuperação pós-lesão"];

$cliente = clienteBuscarPorUsuario($conn, $idUsuario);
$perfilCompleto = $cliente && $cliente['peso'] && $cliente['altura'] && $cliente['id_objetivo'];
$mostrarFormulario = (!$perfilCompleto) || isset($_GET['editar']);

// Seleções atuais, usadas para pré-marcar os checkboxes no modo de edição.
$alergiasAtuais = $intolerAtuais = $condicoesAtuais = [];
$fisicasAtuais = [];

if($cliente){
    foreach(excecaoListarPorCliente($conn, $cliente['id_cliente']) as $excecao){
        if($excecao['tipo'] === "Alergia")      $alergiasAtuais[] = $excecao['descricao'];
        if($excecao['tipo'] === "Intolerancia") $intolerAtuais[]  = $excecao['descricao'];
        if($excecao['tipo'] === "Restricao")    $condicoesAtuais[] = $excecao['descricao'];
    }

    if(!empty($cliente['limitacoes_fisicas'])){
        foreach(explode(",", $cliente['limitacoes_fisicas']) as $item){
            $item = trim($item);
            if($item !== ""){
                $fisicasAtuais[] = $item;
            }
        }
    }
}

if(isset($_POST['salvar_perfil'])){
    $idade    = (int) $_POST['idade'];
    $sexo     = $_POST['sexo'];
    $altura   = (float) $_POST['altura'];
    $peso     = (float) $_POST['peso'];
    $idObjetivo = (int) $_POST['id_objetivo'];

    $alergiasSelecionadas    = $_POST['alergias'] ?? [];
    $intolerSelecionadas     = $_POST['intolerancias'] ?? [];
    $condicoesSelecionadas   = $_POST['condicoes'] ?? [];
    $fisicasSelecionadas     = $_POST['fisicas'] ?? [];

    $dataNasc = date('Y-m-d', strtotime("-$idade years"));

    $limitacoesFisicas = implode(", ", $fisicasSelecionadas);

    $idCliente = clienteSalvar($conn, $idUsuario, $dataNasc, $sexo, $altura, $peso, $idObjetivo, $limitacoesFisicas, $cliente['id_cliente'] ?? null);

    excecaoSalvarSelecionadas($conn, $idCliente, $alergiasSelecionadas, $intolerSelecionadas, $condicoesSelecionadas);

    $imc = imcCalcular($peso, $altura);
    $grupoIdade = geradorDefinirGrupoIdade($idade);

    if($grupoIdade === "ADO"){
        $idClassificacao = null;
    }else{
        $faixaEtaria = ($grupoIdade === "IDO") ? "Idoso" : "Adulto";
        $classificacao = imcClassificarPorTabela($conn, $imc, $faixaEtaria);
        $idClassificacao = $classificacao['id_classificacao'] ?? null;
    }

    imcRegistrar($conn, $idCliente, $imc, $idClassificacao);

    header("Location: home.php");
    exit;
}

$objetivos = objetivoListar($conn);

$idadeAtual = "";
if($cliente && $cliente['data_nasc']){
    $idadeAtual = floor((time() - strtotime($cliente['data_nasc'])) / (365.25 * 24 * 3600));
}

if($perfilCompleto && !$mostrarFormulario){
    $idCliente  = $cliente['id_cliente'];
    $grupoIdade = geradorDefinirGrupoIdade($idadeAtual);

    $ultimoImc = imcBuscarUltimo($conn, $idCliente);
    $valorImc  = (float) $ultimoImc['valor_imc'];
    $classificacaoLabel = $ultimoImc['classificacao'] ?? imcClassificarAdolescente($valorImc, $idadeAtual);

    $objetivoAtual = objetivoBuscarPorId($conn, $cliente['id_objetivo']);
    $codObjetivo = geradorCodigoObjetivo($objetivoAtual['nome'], $grupoIdade);
    $codImc = geradorCodigoImc($classificacaoLabel);

    $plano = geradorBuscarPlano($grupoIdade, $codObjetivo, $codImc);

    $nomesExcecoes = array_merge($alergiasAtuais, $intolerAtuais, $condicoesAtuais);
    $filtrosDieta  = geradorAplicarFiltrosAlimentares($nomesExcecoes);
    $filtrosTreino = geradorAplicarFiltrosFisicos($fisicasAtuais);

    $percentualGauge = ($valorImc - 15) / (40 - 15);
    $percentualGauge = max(0, min(1, $percentualGauge));
    $anguloPonteiro = -90 + ($percentualGauge * 180);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Página inicial</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="card <?php echo $mostrarFormulario ? 'form-dashboard-card' : 'dashboard-mockup-card'; ?>">
        <h1>Olá, <?php echo htmlspecialchars($_SESSION['nome']); ?>!</h1>

        <?php if($mostrarFormulario): ?>

            <form method="post" class="form-grid">

                <div>
                    <label>Idade</label>
                    <input type="number" name="idade" min="13" max="120" value="<?php echo $idadeAtual; ?>" required>
                </div>

                <div>
                    <label>Sexo</label>
                    <select name="sexo" required>
                        <option value="Masculino" <?php echo (isset($cliente['sexo']) && $cliente['sexo'] == 'Masculino') ? 'selected' : ''; ?>>Masculino</option>
                        <option value="Feminino" <?php echo (isset($cliente['sexo']) && $cliente['sexo'] == 'Feminino') ? 'selected' : ''; ?>>Feminino</option>
                        <option value="Outro" <?php echo (isset($cliente['sexo']) && $cliente['sexo'] == 'Outro') ? 'selected' : ''; ?>>Outro</option>
                    </select>
                </div>

                <div>
                    <label>Altura (metros)</label>
                    <input type="number" step="0.01" name="altura" placeholder="Ex: 1.75" value="<?php echo $cliente['altura'] ?? ''; ?>" required>
                </div>

                <div>
                    <label>Peso (kg)</label>
                    <input type="number" step="0.01" name="peso" placeholder="Ex: 70.50" value="<?php echo $cliente['peso'] ?? ''; ?>" required>
                </div>

                <div class="span-2">
                    <label>Objetivo</label>
                    <select name="id_objetivo" required>
                        <?php while($obj = $objetivos->fetch_assoc()): ?>
                            <option value="<?php echo $obj['id_objetivo']; ?>" <?php echo (isset($cliente['id_objetivo']) && $cliente['id_objetivo'] == $obj['id_objetivo']) ? 'selected' : ''; ?>>
                                <?php echo $obj['nome']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="span-2">
                    <label>Restrições alimentares (opcional)</label>
                    <div class="check-grid">
                        <span class="check-group-title">Alergias</span>
                        <?php foreach($opcoesAlergias as $opcao): ?>
                            <label class="check-card">
                                <input type="checkbox" name="alergias[]" value="<?php echo $opcao; ?>" <?php echo chk($opcao, $alergiasAtuais); ?>>
                                <span class="check-card-box"><?php echo $opcao; ?></span>
                            </label>
                        <?php endforeach; ?>

                        <span class="check-group-title">Intolerâncias</span>
                        <?php foreach($opcoesIntolerancias as $opcao): ?>
                            <label class="check-card">
                                <input type="checkbox" name="intolerancias[]" value="<?php echo $opcao; ?>" <?php echo chk($opcao, $intolerAtuais); ?>>
                                <span class="check-card-box"><?php echo $opcao; ?></span>
                            </label>
                        <?php endforeach; ?>

                        <span class="check-group-title">Preferenciais / Condições</span>
                        <?php foreach($opcoesCondicoes as $opcao): ?>
                            <label class="check-card">
                                <input type="checkbox" name="condicoes[]" value="<?php echo $opcao; ?>" <?php echo chk($opcao, $condicoesAtuais); ?>>
                                <span class="check-card-box"><?php echo $opcao; ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="span-2">
                    <label>Limitações físicas (opcional)</label>
                    <div class="check-grid">
                        <?php foreach($opcoesFisicas as $opcao): ?>
                            <label class="check-card">
                                <input type="checkbox" name="fisicas[]" value="<?php echo $opcao; ?>" <?php echo chk($opcao, $fisicasAtuais); ?>>
                                <span class="check-card-box"><?php echo $opcao; ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="span-2">
                    <input type="submit" name="salvar_perfil" value="Salvar e calcular IMC">
                </div>

            </form>

        <?php else: ?>

            <?php if($plano && $plano['status'] === 'Protegido'): ?>
                <div class="resultado tag-warning-box fade-in-item">
                    <strong>Perfil que exige atenção profissional.</strong>
                    Este objetivo não é aplicado automaticamente para o seu perfil de IMC.
                    O plano abaixo foi ajustado para foco em saúde geral — procure um nutricionista
                    ou profissional de educação física para uma orientação individualizada.
                </div>
            <?php endif; ?>

            <div class="mockup-grid-3">

                <div class="mockup-card fade-in-item">
                    <div class="mockup-card-header">
                        <span>RESUMO DE IMC</span>
                        <span class="card-icon">📊</span>
                    </div>
                    <div class="imc-value-large"><?php echo number_format($valorImc, 2, ',', '.'); ?></div>
                    <div class="arc-container">
                        <div class="arc-gauge"></div>
                        <div class="arc-needle" style="transform: rotate(<?php echo $anguloPonteiro; ?>deg);"></div>
                    </div>
                    <div class="arc-labels">
                        <span>Abaixo</span><span>Ideal</span><span>Acima</span>
                    </div>
                    <div class="imc-sub-info">
                        <strong><?php echo htmlspecialchars($classificacaoLabel); ?></strong>
                        <p>Objetivo: <span><?php echo htmlspecialchars($objetivoAtual['nome']); ?></span></p>
                    </div>
                </div>

                <div class="mockup-card fade-in-item">
                    <div class="mockup-card-header">
                        <span>SUGESTÃO DE DIETA</span>
                        <span class="card-icon">🥗</span>
                    </div>
                    <?php if($plano): ?>
                        <ul class="mockup-list">
                            <?php if($filtrosDieta['bloqueado']): ?>
                                <li>Personalização bloqueada por segurança: siga apenas a orientação geral abaixo e busque acompanhamento profissional.</li>
                            <?php else: ?>
                                <li><?php echo htmlspecialchars($plano['dieta_base']); ?></li>
                                <li><?php echo htmlspecialchars($plano['observacao']); ?></li>
                            <?php endif; ?>
                            <?php foreach($filtrosDieta['ajustes'] as $ajuste): ?>
                                <li><?php echo $ajuste; ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <p class="codigo-plano">Código: <?php echo $plano['codigo_dieta']; ?></p>
                    <?php else: ?>
                        <p>Não foi possível localizar um plano para esta combinação. Atualize seus dados.</p>
                    <?php endif; ?>
                </div>

                <div class="mockup-card fade-in-item">
                    <div class="mockup-card-header">
                        <span>SUGESTÃO DE TREINO</span>
                        <span class="card-icon">🏋️</span>
                    </div>
                    <?php if($plano): ?>
                        <ul class="mockup-list">
                            <li><?php echo htmlspecialchars($plano['treino_base']); ?></li>
                            <li><?php echo htmlspecialchars($plano['orientacao']); ?></li>
                            <?php foreach($filtrosTreino['ajustes'] as $ajuste): ?>
                                <li><?php echo $ajuste; ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <p class="codigo-plano">Código: <?php echo $plano['codigo_treino']; ?></p>
                    <?php else: ?>
                        <p>Não foi possível localizar um plano para esta combinação. Atualize seus dados.</p>
                    <?php endif; ?>
                </div>

            </div>

            <div class="mockup-footer-actions">
                <a href="home.php?editar=1" class="btn-gear">⚙️ Atualizar meus dados</a>
            </div>

        <?php endif; ?>

        <div class="links">
            <a href="logout.php">Sair</a> | <a href="excluir_conta.php" class="link-danger">Excluir minha conta</a>
        </div>
    </div>

</body>
</html>
