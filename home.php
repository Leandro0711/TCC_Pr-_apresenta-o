<?php
require_once("services/conn.php");
require_once("services/ClienteRepository.php");
require_once("services/ImcRepository.php");
require_once("services/ExcecaoRepository.php");
require_once("services/GeradorService.php");
require_once("partials/formatar-plano.php");
session_start();

if(!isset($_SESSION['id_usuario'])){
    header("Location: login.php?volta=resultado");
    exit;
}

$idUsuario = $_SESSION['id_usuario'];

// Perfil, opções dos checkboxes, seleções atuais, objetivos e idade
// (mesmo código que ficava aqui, agora compartilhado com a home)
require("partials/perfil-dados.php");

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

// Sem perfil completo ainda não há resultado: abre o gerador na página inicial.
if(!$perfilCompleto){
    header("Location: index.php?gerador=1");
    exit;
}

// "?editar=1" (links antigos) abre o gerador por cima do resultado.
$abrirGerador = isset($_GET['editar']);

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

$tituloPagina = "Seu plano · HealthCore";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<?php include "partials/head.php"; ?>
</head>
<body>

<?php include "partials/header.php"; ?>

    <main id="conteudo" class="pagina resultado">

        <div class="barra-voltar">
            <a class="botao botao-texto" href="index.php"><span aria-hidden="true">←</span> Voltar</a>
            <?php logo(); ?>
        </div>

        <div class="resultado-cabecalho">
            <h1>Seu plano</h1>
            <p>Olá, <?php echo htmlspecialchars($_SESSION['nome']); ?>. Este é o resumo montado a partir dos dados que você informou.</p>
        </div>

        <?php if($plano && $plano['status'] === 'Protegido'): ?>
            <div class="aviso" role="note">
                <strong>Perfil que exige atenção profissional.</strong>
                Este objetivo não é aplicado automaticamente para o seu perfil de IMC.
                O plano abaixo foi ajustado para foco em saúde geral — procure um nutricionista
                ou profissional de educação física para uma orientação individualizada.
            </div>
        <?php endif; ?>

        <section class="imc-bloco" aria-labelledby="imc-titulo">
            <h2 id="imc-titulo" class="visualmente-oculto">Seu IMC</h2>
            <div class="imc-dados">
                <p class="imc-valor"><span class="visualmente-oculto">IMC </span><?php echo number_format($valorImc, 2, ',', '.'); ?></p>
                <p class="imc-info">
                    <span class="imc-rotulo">Classificação</span>
                    <strong><?php echo htmlspecialchars($classificacaoLabel); ?></strong>
                </p>
                <p class="imc-info">
                    <span class="imc-rotulo">Objetivo</span>
                    <strong><?php echo htmlspecialchars($objetivoAtual['nome']); ?></strong>
                </p>
            </div>
            <p class="imc-nota">
                O IMC relaciona peso e altura e serve como uma referência geral. Ele não mede
                composição corporal nem condicionamento, então use este resultado como ponto
                de partida para as sugestões abaixo.
            </p>
        </section>

        <div class="colunas">
            <section class="coluna" aria-labelledby="titulo-treino">
                <h2 id="titulo-treino">Treino</h2>
                <?php if($plano): ?>
                    <h3 class="coluna-subtitulo">Orientações</h3>
                    <ul class="lista-pontos">
                        <li><?php echo htmlspecialchars($plano['treino_base']); ?></li>
                        <li><?php echo htmlspecialchars($plano['orientacao']); ?></li>
                        <?php foreach($filtrosTreino['ajustes'] as $ajuste): ?>
                            <li><?php echo $ajuste; ?></li>
                        <?php endforeach; ?>
                    </ul>

                    <?php if($plano['exercicios']): ?>
                        <h3 class="coluna-subtitulo">Exercícios</h3>
                        <ul class="lista-plano">
                            <?php foreach($plano['exercicios'] as [$etapa, $exercicio, $dose]): ?>
                                <li>
                                    <span class="lista-plano-rotulo"><?php echo htmlspecialchars($etapa); ?></span>
                                    <span><strong><?php echo htmlspecialchars($exercicio); ?></strong>: <?php echo htmlspecialchars(formatarDose($dose)); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                <?php else: ?>
                    <p>Não foi possível localizar um plano para esta combinação. Atualize seus dados.</p>
                <?php endif; ?>
            </section>

            <section class="coluna" aria-labelledby="titulo-dieta">
                <h2 id="titulo-dieta">Dieta</h2>
                <?php if($plano): ?>
                    <h3 class="coluna-subtitulo">Orientações</h3>
                    <ul class="lista-pontos">
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

                    <?php // Com a personalização bloqueada (doença renal), o cardápio-modelo não é exibido ?>
                    <?php if($plano['refeicoes'] && !$filtrosDieta['bloqueado']): ?>
                        <h3 class="coluna-subtitulo">Refeições do dia</h3>
                        <ul class="lista-plano">
                            <?php foreach($plano['refeicoes'] as [$momento, $refeicao]): ?>
                                <li>
                                    <span class="lista-plano-rotulo"><?php echo htmlspecialchars($momento); ?></span>
                                    <span><?php echo htmlspecialchars($refeicao); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                <?php else: ?>
                    <p>Não foi possível localizar um plano para esta combinação. Atualize seus dados.</p>
                <?php endif; ?>
            </section>
        </div>

        <div class="acoes-resultado">
            <button type="button" class="botao botao-secundario" commandfor="modal-gerador" command="show-modal">Atualizar meus dados</button>
        </div>

        <!-- Espaço para texto adicional: edite livremente o conteúdo desta seção -->
        <section class="texto-adicional" aria-labelledby="titulo-adicional">
            <h2 id="titulo-adicional">Antes de começar</h2>
            <p>
                Estas sugestões são uma orientação geral e não substituem uma avaliação individual.
                Comece no seu ritmo, observe como o corpo responde e, sempre que possível, converse
                com um nutricionista ou profissional de educação física.
            </p>
        </section>

    </main>

<?php include "partials/rodape.php"; ?>

<?php include "partials/modal-gerador.php"; ?>

</body>
</html>
