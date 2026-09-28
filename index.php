<?php
session_start();

$logado = isset($_SESSION['id_usuario']);

// Logado: carrega os dados do perfil para o modal do gerador abrir já preenchido.
if($logado){
    require_once("services/conn.php");
    $idUsuario = $_SESSION['id_usuario'];
    require("partials/perfil-dados.php");
    $abrirGerador = isset($_GET['gerador']);
}

// Abas da home (sem JS): a escolhida vem por ?aba=, com lista fixa
$abas = [
    "atividade"  => "Atividade física",
    "exercicios" => "Exercícios",
    "habitos"    => "Hábitos",
    "como"       => "Como funciona",
];
$abaAtual = array_key_exists($_GET['aba'] ?? "", $abas) ? $_GET['aba'] : "atividade";

$tituloPagina = "HealthCore · Saúde e qualidade de vida";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<?php include "partials/head.php"; ?>
</head>
<body>

<?php include "partials/header.php"; ?>

    <main id="conteudo">

        <section class="hero">
            <?php logo(true, false); ?>
            <h1>Hábitos saudáveis começam aqui</h1>
            <p class="hero-texto">
                O HealthCore ajuda você a organizar sua rotina de saúde, acompanhar seu IMC
                e manter pequenos hábitos que fazem diferença na sua qualidade de vida.
            </p>
        </section>

        <!-- Abas sem JS: cada aba é um link (?aba=...) e o PHP mostra só o painel escolhido.
             O "#abas" mantém a rolagem na seção depois de trocar. -->
        <section class="secao-abas" id="abas" aria-label="Saiba mais">
            <nav class="abas" aria-label="Temas">
                <?php foreach($abas as $chave => $rotulo): ?>
                    <a class="aba" href="index.php?aba=<?php echo $chave; ?>#abas" <?php echo $chave === $abaAtual ? 'aria-current="page"' : ''; ?>><?php echo $rotulo; ?></a>
                <?php endforeach; ?>
            </nav>

            <?php if($abaAtual === "atividade"): ?>
            <div class="painel-aba" id="painel-atividade">
                <h2>A importância da atividade física</h2>
                <p>
                    Praticar exercícios com regularidade é um dos hábitos mais importantes para
                    a qualidade de vida. Além de ajudar no condicionamento do corpo, a atividade
                    física contribui para o bom funcionamento do coração, para a disposição no
                    dia a dia e para o equilíbrio emocional.
                </p>
                <ul class="etiquetas" aria-label="Benefícios">
                    <li class="etiqueta">Disposição</li>
                    <li class="etiqueta">Condicionamento físico</li>
                    <li class="etiqueta">Saúde cardiovascular</li>
                    <li class="etiqueta">Bem-estar</li>
                </ul>
            </div>

            <?php elseif($abaAtual === "exercicios"): ?>
            <div class="painel-aba" id="painel-exercicios">
                <h2>Exercícios para o seu dia a dia</h2>
                <p>
                    Não é preciso ir à academia todos os dias para se manter ativo. Pequenas
                    mudanças na rotina já fazem diferença:
                </p>
                <div class="cartoes">
                    <div class="cartao">
                        <h3>Caminhada</h3>
                        <p>Uma caminhada de 20 a 30 minutos já ajuda a melhorar o condicionamento e reduzir o estresse.</p>
                    </div>
                    <div class="cartao">
                        <h3>Alongamentos</h3>
                        <p>Alongar o corpo pela manhã ou após longos períodos sentado alivia tensões musculares.</p>
                    </div>
                    <div class="cartao">
                        <h3>Subir escadas</h3>
                        <p>Trocar o elevador pela escada é uma forma simples de incluir mais movimento no dia.</p>
                    </div>
                    <div class="cartao">
                        <h3>Pausas ativas</h3>
                        <p>Levantar e se movimentar a cada hora de trabalho ajuda a evitar o sedentarismo.</p>
                    </div>
                    <div class="cartao">
                        <h3>Atividades recreativas</h3>
                        <p>Dançar, jogar ou praticar um esporte com amigos também conta como atividade física.</p>
                    </div>
                </div>
            </div>

            <?php elseif($abaAtual === "habitos"): ?>
            <div class="painel-aba" id="painel-habitos">
                <h2>Hábitos saudáveis no dia a dia</h2>
                <ul class="lista-pontos">
                    <li>Alimentação equilibrada, com variedade de nutrientes</li>
                    <li>Boa hidratação ao longo do dia</li>
                    <li>Sono adequado para a recuperação do corpo</li>
                    <li>Prática regular de atividades físicas</li>
                    <li>Uma rotina organizada, com espaço para o bem-estar</li>
                </ul>
            </div>

            <?php else: ?>
            <div class="painel-aba" id="painel-como">
                <h2>Como o HealthCore monta o seu plano</h2>
                <p>Em três passos, sem complicação.</p>
                <ol class="passos">
                    <li>
                        <div>
                            <h3>Você conta um pouco sobre você</h3>
                            <p>Idade, altura, peso e objetivo. Se quiser, também restrições alimentares e limitações físicas.</p>
                        </div>
                    </li>
                    <li>
                        <div>
                            <h3>Calculamos seu IMC</h3>
                            <p>O cálculo considera a sua faixa de idade: adolescente, adulto ou idoso, porque cada fase tem sua referência.</p>
                        </div>
                    </li>
                    <li>
                        <div>
                            <h3>Você recebe sugestões de treino e dieta</h3>
                            <p>O plano parte da combinação entre idade, objetivo e IMC e é ajustado às restrições que você marcou. Quando o objetivo pede cuidado extra, o plano prioriza a saúde geral e indica procurar um profissional.</p>
                        </div>
                    </li>
                </ol>
            </div>
            <?php endif; ?>
        </section>

        <!-- Chamada para o gerador: fica no fim, depois das informações de saúde -->
        <section class="chamada-final">
            <h2>Pronto para começar?</h2>
            <?php if($logado): ?>
                <p>Com seus dados, montamos sugestões de treino e dieta para o seu momento.</p>
                <button type="button" class="botao botao-primario botao--grande" commandfor="modal-gerador" command="show-modal">Gerar meu treino e dieta</button>
            <?php else: ?>
                <p>Crie sua conta gratuita e comece a acompanhar sua saúde com o HealthCore.</p>
                <a class="botao botao-primario botao--grande" href="login.php?volta=gerador">Gerar meu treino e dieta</a>
                <p class="chamada-final-conta">Ainda não tem conta? <a href="cadastro.php?volta=gerador">Crie a sua</a></p>
            <?php endif; ?>
        </section>

    </main>

<?php include "partials/rodape.php"; ?>

<?php if($logado) include "partials/modal-gerador.php"; ?>

</body>
</html>
