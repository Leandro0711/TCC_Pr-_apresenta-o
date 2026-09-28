<?php
// Gerador de dieta/treino: idade+objetivo+IMC -> código padronizado
// (D-[GRUPO]-[OBJETIVO]-[IMC] / T-[GRUPO]-[OBJETIVO]-[IMC]) -> plano-base
// do catálogo, com filtros de exceções alimentares e físicas aplicados por
// cima. Baseado nos documentos de planejamento do TCC.

function geradorDefinirGrupoIdade($idade){
    if($idade >= 60) return "IDO";
    if($idade >= 19) return "ADU";
    return "ADO"; // 13 a 18 anos
}

// O formulário usa um único seletor de objetivo; "Ganhar massa muscular"
// e "Fortalecer músculos" são o mesmo objetivo tratado de forma diferente
// para idosos (catálogo não tem código GM para esse grupo, só FM).
function geradorCodigoObjetivo($nomeObjetivo, $grupoIdade){
    $mapa = [
        "Perder gordura"          => "PG",
        "Ganhar peso saudável"    => "GP",
        "Ganhar massa muscular / Fortalecer músculos" => ($grupoIdade === "IDO") ? "FM" : "GM",
        "Saúde e bem-estar"       => "SB",
    ];
    return $mapa[$nomeObjetivo] ?? "SB";
}

// Converte o texto salvo em classificacao_imc (ou o rótulo do adolescente)
// no código curto usado nos códigos de dieta/treino.
function geradorCodigoImc($classificacaoTexto){
    $mapa = [
        "Abaixo do peso" => "BXP",
        "Baixo peso"     => "BXP",
        "Magreza"        => "MAG",
        "Peso normal"    => "ADE",
        "Peso adequado"  => "ADE",
        "IMC adequado"   => "ADE",
        "Sobrepeso"      => "SOB",
        "Obesidade"      => "OBE",
    ];
    return $mapa[$classificacaoTexto] ?? "ADE";
}

// Catálogo-base: 44 combinações (16 adolescente + 16 adulto + 12 idoso).
// dieta_base/treino_base são compartilhados por grupo+objetivo; status,
// observação e sufixo "-SEG" variam por categoria de IMC.
function geradorCatalogo(){
    $orientacaoAdo = "Não realize testes máximos de força. Pare se sentir dor, tontura, falta de ar incomum ou mal-estar. A prática deve ser adequada ao ambiente e à supervisão disponível.";
    $orientacaoAdu = "Descanso sugerido de 60 a 90 segundos entre séries. Aumente repetições ou resistência apenas sem dor e mantendo a técnica. Para qualquer condição clínica, siga o aviso do sistema.";
    $orientacaoIdo = "Realize os exercícios próximo a um apoio estável e não use cadeira com rodinhas. Interrompa em caso de dor, tontura, desmaio, falta de ar incomum ou dor no peito, e procure avaliação. Em recuperação, use o plano adaptado de baixa intensidade.";

    $notaAdo = "Para adolescentes, qualquer mudança importante na alimentação deve envolver o responsável e, quando necessário, um profissional de saúde.";
    $notaIdo = "Fique atento à aceitação alimentar, à mastigação, à hidratação e a qualquer perda de peso não intencional.";

    $dietaPG = "Organize refeições equilibradas, aumente a presença de vegetais, feijão/leguminosas e preparações caseiras, e reduza o consumo de ultraprocessados e bebidas açucaradas.";
    $dietaGP = "Apoie o ganho de peso gradual e saudável com refeições regulares, combinações mais completas e lanches adicionais quando necessário.";
    $dietaGM = "Apoie a rotina de fortalecimento muscular com refeições completas, boas fontes de proteína ao longo do dia e carboidratos suficientes para os treinos.";
    $dietaSB = "Mantenha uma rotina alimentar equilibrada e sustentável, compatível com o dia a dia e a prática regular de atividade física.";

    $obsProtegidoPG = "Plano protegido: sem foco em redução de peso; fique atento a perda de peso não intencional, cansaço ou baixa ingestão, e procure avaliação profissional.";
    $obsAdequadoPG  = "Foco em hábitos e composição corporal, sem promessa de emagrecimento rápido.";
    $obsSobrepesoPG = "Organize os horários, evite pular refeições para compensar excessos e prefira água a bebidas açucaradas. Evite dietas extremas.";

    $obsBaixoPesoGP    = "Foco em regularidade, boa aceitação e ganho gradual. Perda de peso não intencional exige avaliação profissional.";
    $obsAdequadoGP     = "O objetivo é o ganho gradual, sem consumo excessivo de ultraprocessados.";
    $obsProtegidoGP    = "Plano protegido: não sugerir aumento de peso automático; priorize refeições equilibradas, fortalecimento e avaliação individual.";

    $obsBaixoPesoGM = "Combine fortalecimento progressivo com refeições completas e regulares; atenção à perda de peso não intencional.";
    $obsAdequadoGM  = "Priorize constância e refeições completas; suplementos não são necessários nesta primeira versão do sistema.";
    $obsRecomposicaoGM = "Foco em fortalecimento e recomposição corporal, sem superávit calórico automático.";

    $obsBaixoPesoSB = "Atenção à regularidade alimentar e à manutenção do peso; perda não intencional exige avaliação profissional.";
    $obsAdequadoSB  = "Foco na manutenção de hábitos saudáveis e em uma rotina sustentável.";
    $obsSobrepesoSB = "Foco em hábitos sustentáveis e no aumento gradual de movimento, sem restrição extrema.";

    return [
        "ADO" => [
            "PG" => [
                "dieta_base" => $dietaPG, "orientacao" => $orientacaoAdo, "nota" => $notaAdo,
                "treino_base" => "3 dias por semana, em dias alternados (nos perfis de sobrepeso/obesidade, inclua caminhada ou outra atividade prazerosa nos demais dias, conforme a tolerância).",
                "itens" => [
                    "MAG" => ["sufixo" => "-SEG", "status" => "Protegido", "observacao" => $obsProtegidoPG],
                    "ADE" => ["sufixo" => "",     "status" => "Normal",    "observacao" => $obsAdequadoPG],
                    "SOB" => ["sufixo" => "",     "status" => "Normal",    "observacao" => $obsSobrepesoPG],
                    "OBE" => ["sufixo" => "-SEG", "status" => "Protegido", "observacao" => $obsSobrepesoPG],
                ],
            ],
            "GP" => [
                "dieta_base" => $dietaGP, "orientacao" => $orientacaoAdo, "nota" => $notaAdo,
                "treino_base" => "3 dias por semana, em dias alternados, sem treinar até a exaustão.",
                "itens" => [
                    "MAG" => ["sufixo" => "",     "status" => "Normal",    "observacao" => $obsBaixoPesoGP],
                    "ADE" => ["sufixo" => "",     "status" => "Normal",    "observacao" => $obsAdequadoGP],
                    "SOB" => ["sufixo" => "-SEG", "status" => "Protegido", "observacao" => $obsProtegidoGP],
                    "OBE" => ["sufixo" => "-SEG", "status" => "Protegido", "observacao" => $obsProtegidoGP],
                ],
            ],
            "GM" => [
                "dieta_base" => $dietaGM, "orientacao" => $orientacaoAdo, "nota" => $notaAdo,
                "treino_base" => "3 dias por semana, em dias alternados, priorizando técnica e progressão lenta.",
                "itens" => [
                    "MAG" => ["sufixo" => "", "status" => "Normal",    "observacao" => $obsBaixoPesoGM],
                    "ADE" => ["sufixo" => "", "status" => "Normal",    "observacao" => $obsAdequadoGM],
                    "SOB" => ["sufixo" => "", "status" => "Normal",    "observacao" => $obsRecomposicaoGM],
                    "OBE" => ["sufixo" => "", "status" => "Protegido", "observacao" => $obsRecomposicaoGM],
                ],
            ],
            "SB" => [
                "dieta_base" => $dietaSB, "orientacao" => $orientacaoAdo, "nota" => $notaAdo,
                "treino_base" => "3 dias por semana, somando movimento prazeroso ao cotidiano.",
                "itens" => [
                    "MAG" => ["sufixo" => "",     "status" => "Normal",    "observacao" => $obsBaixoPesoSB],
                    "ADE" => ["sufixo" => "",     "status" => "Normal",    "observacao" => $obsAdequadoSB],
                    "SOB" => ["sufixo" => "",     "status" => "Normal",    "observacao" => $obsSobrepesoSB],
                    "OBE" => ["sufixo" => "-SEG", "status" => "Protegido", "observacao" => $obsSobrepesoSB],
                ],
            ],
        ],
        "ADU" => [
            "PG" => [
                "dieta_base" => $dietaPG, "orientacao" => $orientacaoAdu, "nota" => "",
                "treino_base" => "3 dias por semana, em dias alternados (nos perfis de sobrepeso/obesidade, inclua um movimento leve adicional conforme a tolerância).",
                "itens" => [
                    "BXP" => ["sufixo" => "-SEG", "status" => "Protegido", "observacao" => $obsProtegidoPG],
                    "ADE" => ["sufixo" => "",     "status" => "Normal",    "observacao" => $obsAdequadoPG],
                    "SOB" => ["sufixo" => "",     "status" => "Normal",    "observacao" => $obsSobrepesoPG],
                    "OBE" => ["sufixo" => "",     "status" => "Protegido", "observacao" => $obsSobrepesoPG],
                ],
            ],
            "GP" => [
                "dieta_base" => $dietaGP, "orientacao" => $orientacaoAdu, "nota" => "",
                "treino_base" => "3 dias por semana, em dias alternados, com foco em força e recuperação.",
                "itens" => [
                    "BXP" => ["sufixo" => "",     "status" => "Normal",    "observacao" => $obsBaixoPesoGP],
                    "ADE" => ["sufixo" => "",     "status" => "Normal",    "observacao" => $obsAdequadoGP],
                    "SOB" => ["sufixo" => "-SEG", "status" => "Protegido", "observacao" => $obsProtegidoGP],
                    "OBE" => ["sufixo" => "-SEG", "status" => "Protegido", "observacao" => $obsProtegidoGP],
                ],
            ],
            "GM" => [
                "dieta_base" => $dietaGM, "orientacao" => $orientacaoAdu, "nota" => "",
                "treino_base" => "3 dias por semana, em dias alternados; aumente a dificuldade apenas quando a técnica estiver estável.",
                "itens" => [
                    "BXP" => ["sufixo" => "", "status" => "Normal",    "observacao" => $obsBaixoPesoGM],
                    "ADE" => ["sufixo" => "", "status" => "Normal",    "observacao" => $obsAdequadoGM],
                    "SOB" => ["sufixo" => "", "status" => "Normal",    "observacao" => $obsRecomposicaoGM],
                    "OBE" => ["sufixo" => "", "status" => "Protegido", "observacao" => $obsRecomposicaoGM],
                ],
            ],
            "SB" => [
                "dieta_base" => $dietaSB, "orientacao" => $orientacaoAdu, "nota" => "",
                "treino_base" => "3 dias por semana, com foco em constância e bem-estar.",
                "itens" => [
                    "BXP" => ["sufixo" => "", "status" => "Normal",    "observacao" => $obsBaixoPesoSB],
                    "ADE" => ["sufixo" => "", "status" => "Normal",    "observacao" => $obsAdequadoSB],
                    "SOB" => ["sufixo" => "", "status" => "Normal",    "observacao" => $obsSobrepesoSB],
                    "OBE" => ["sufixo" => "", "status" => "Protegido", "observacao" => $obsSobrepesoSB],
                ],
            ],
        ],
        "IDO" => [
            "PG" => [
                "dieta_base" => $dietaPG, "orientacao" => $orientacaoIdo, "nota" => $notaIdo,
                "treino_base" => "3 dias por semana, em dias alternados, em ritmo confortável, priorizando baixo impacto.",
                "itens" => [
                    "BXP" => ["sufixo" => "-SEG", "status" => "Protegido", "observacao" => $obsProtegidoPG],
                    "ADE" => ["sufixo" => "",     "status" => "Normal",    "observacao" => $obsAdequadoPG],
                    "SOB" => ["sufixo" => "",     "status" => "Normal",    "observacao" => $obsSobrepesoPG],
                ],
            ],
            "GP" => [
                "dieta_base" => $dietaGP, "orientacao" => $orientacaoIdo, "nota" => $notaIdo,
                "treino_base" => "2 a 3 dias por semana, em dias alternados, em intensidade leve a moderada.",
                "itens" => [
                    "BXP" => ["sufixo" => "",     "status" => "Normal",    "observacao" => $obsBaixoPesoGP],
                    "ADE" => ["sufixo" => "",     "status" => "Normal",    "observacao" => $obsAdequadoGP],
                    "SOB" => ["sufixo" => "-SEG", "status" => "Protegido", "observacao" => $obsProtegidoGP],
                ],
            ],
            "FM" => [
                "dieta_base" => $dietaGM, "orientacao" => $orientacaoIdo, "nota" => $notaIdo,
                "treino_base" => "2 a 3 dias por semana, em dias alternados, com foco em autonomia e força funcional.",
                "itens" => [
                    "BXP" => ["sufixo" => "", "status" => "Normal", "observacao" => $obsBaixoPesoGM],
                    "ADE" => ["sufixo" => "", "status" => "Normal", "observacao" => $obsAdequadoGM],
                    "SOB" => ["sufixo" => "", "status" => "Normal", "observacao" => $obsRecomposicaoGM],
                ],
            ],
            "SB" => [
                "dieta_base" => $dietaSB, "orientacao" => $orientacaoIdo, "nota" => $notaIdo,
                "treino_base" => "2 a 3 dias por semana, com foco em mobilidade, força e equilíbrio.",
                "itens" => [
                    "BXP" => ["sufixo" => "", "status" => "Normal", "observacao" => $obsBaixoPesoSB],
                    "ADE" => ["sufixo" => "", "status" => "Normal", "observacao" => $obsAdequadoSB],
                    "SOB" => ["sufixo" => "", "status" => "Normal", "observacao" => $obsSobrepesoSB],
                ],
            ],
        ],
    ];
}

// Exercícios de cada treino-base: [etapa, exercício, séries/repetições/tempo].
// Transcritos da "Biblioteca de Planos de Treino e Alimentação" do TCC.
// Chave "GRUPO-OBJETIVO"; quando o documento traz uma tabela diferente para
// uma categoria de IMC, ela entra também como "GRUPO-OBJETIVO-IMC".
function geradorTreinos(){
    $treinoAdoPg = [
        ["Aquecimento", "Marcha no lugar, mobilidade de ombros e quadris", "5-8 min"],
        ["Força", "Agachamento até cadeira", "2-3 x 8-12"],
        ["Força", "Flexão na parede ou banco", "2-3 x 8-12"],
        ["Força", "Remada com elástico", "2-3 x 8-12"],
        ["Core", "Prancha com joelhos apoiados", "2 x 15-20 s"],
        ["Cardio", "Caminhada rápida, dança ou bicicleta", "10-15 min"],
        ["Final", "Respiração e alongamentos leves", "3-5 min"],
    ];

    $treinoAdoPgSobObe = [
        ["Aquecimento", "Caminhada leve ou marcha no lugar", "5-8 min"],
        ["Força", "Sentar e levantar da cadeira", "2-3 x 8-12"],
        ["Força", "Flexão na parede", "2-3 x 8-12"],
        ["Força", "Remada com elástico (ou puxada isométrica leve)", "2-3 x 8-12"],
        ["Força", "Ponte de glúteos", "2-3 x 10-12"],
        ["Cardio baixo impacto", "Caminhada, bicicleta leve ou dança sem saltos", "10-20 min"],
        ["Final", "Alongamentos leves e respiração", "3-5 min"],
    ];

    $treinoAdoGp = [
        ["Aquecimento", "Marcha e mobilidade articular", "5-8 min"],
        ["Força", "Agachamento até cadeira", "2-3 x 8-12"],
        ["Força", "Ponte de glúteos", "2-3 x 10-12"],
        ["Força", "Flexão na parede/banco", "2-3 x 8-12"],
        ["Força", "Remada com elástico", "2-3 x 8-12"],
        ["Core", "Bird-dog", "2 x 6-8 por lado"],
        ["Final", "Caminhada leve e alongamento", "5 min"],
    ];

    $treinoAdoGm = [
        ["Aquecimento", "Marcha, mobilidade de quadril e ombro", "5-8 min"],
        ["Força", "Agachamento até cadeira ou livre", "3 x 8-12"],
        ["Força", "Ponte de glúteos", "3 x 10-12"],
        ["Força", "Flexão na parede/banco", "3 x 8-12"],
        ["Força", "Remada com elástico", "3 x 8-12"],
        ["Core", "Prancha com apoio nos joelhos", "2 x 15-20 s"],
        ["Final", "Mobilidade leve", "3-5 min"],
    ];

    $treinoAdoSb = [
        ["Aquecimento", "Marcha e mobilidade geral", "5 min"],
        ["Força", "Sentar e levantar da cadeira", "2 x 8-12"],
        ["Força", "Flexão na parede", "2 x 8-12"],
        ["Força", "Remada com elástico", "2 x 8-12"],
        ["Equilíbrio", "Apoio em um pé com apoio próximo", "2 x 15 s por lado"],
        ["Cardio", "Dança, caminhada ou bicicleta", "10-20 min"],
        ["Final", "Respiração e alongamento leve", "3-5 min"],
    ];

    $treinoAduPg = [
        ["Aquecimento", "Caminhada e mobilidade geral", "5-10 min"],
        ["Força", "Agachamento até cadeira/livre", "3 x 8-12"],
        ["Força", "Flexão na parede/banco", "3 x 8-12"],
        ["Força", "Remada com elástico", "3 x 8-12"],
        ["Força", "Ponte de glúteos", "3 x 10-12"],
        ["Core", "Prancha adaptada", "2 x 15-30 s"],
        ["Cardio", "Caminhada rápida, bicicleta ou dança", "15-20 min"],
    ];

    $treinoAduPgSobObe = [
        ["Aquecimento", "Caminhada leve ou marcha no lugar", "5-10 min"],
        ["Força", "Agachamento até cadeira", "3 x 8-12"],
        ["Força", "Ponte de glúteos", "3 x 10-12"],
        ["Força", "Flexão na parede/banco", "3 x 8-12"],
        ["Força", "Remada com elástico", "3 x 8-12"],
        ["Core", "Bird-dog", "2 x 8 por lado"],
        ["Cardio baixo impacto", "Caminhada, bicicleta ergométrica ou dança", "15-25 min"],
    ];

    $treinoAduGp = [
        ["Aquecimento", "Caminhada leve e mobilidade", "5-8 min"],
        ["Força", "Agachamento até cadeira/livre", "3 x 8-12"],
        ["Força", "Ponte de glúteos", "3 x 10-12"],
        ["Força", "Flexão na parede/banco", "3 x 8-12"],
        ["Força", "Remada com elástico", "3 x 8-12"],
        ["Força", "Elevação de panturrilha com apoio", "2-3 x 12-15"],
        ["Final", "Caminhada leve e alongamentos leves", "5 min"],
    ];

    $treinoAduGm = [
        ["Aquecimento", "Marcha e mobilidade de quadril/ombro", "5-8 min"],
        ["Força", "Agachamento livre ou até cadeira", "3 x 8-12"],
        ["Força", "Avanço curto com apoio ou step-up baixo", "3 x 8 por lado"],
        ["Força", "Flexão inclinada na parede/banco", "3 x 8-12"],
        ["Força", "Remada com elástico", "3 x 8-12"],
        ["Força", "Ponte de glúteos", "3 x 10-15"],
        ["Core", "Prancha adaptada ou dead bug", "2 x 15-30 s / 8 por lado"],
    ];

    $treinoAduSb = [
        ["Aquecimento", "Caminhada leve e mobilidade geral", "5-8 min"],
        ["Força", "Sentar e levantar da cadeira", "2-3 x 8-12"],
        ["Força", "Flexão na parede", "2-3 x 8-12"],
        ["Força", "Remada com elástico", "2-3 x 8-12"],
        ["Força", "Ponte de glúteos", "2-3 x 10-12"],
        ["Cardio", "Caminhada, bicicleta ou dança", "15-25 min"],
        ["Final", "Alongamento leve", "3-5 min"],
    ];

    $treinoIdoPg = [
        ["Aquecimento", "Marcha sentada ou em pé com apoio", "5 min"],
        ["Força funcional", "Sentar e levantar da cadeira", "2 x 6-10"],
        ["Força", "Flexão na parede", "2 x 6-10"],
        ["Força", "Remada com elástico leve", "2 x 8-10"],
        ["Equilíbrio", "Passos laterais segurando apoio", "2 x 8 por lado"],
        ["Cardio", "Caminhada leve com apoio necessário", "8-15 min"],
        ["Final", "Respiração e mobilidade leve", "3 min"],
    ];

    $treinoIdoGp = [
        ["Aquecimento", "Marcha sentada ou caminhada lenta", "5 min"],
        ["Força funcional", "Sentar e levantar da cadeira", "2 x 6-10"],
        ["Força", "Ponte de glúteos ou extensão de quadril em pé com apoio", "2 x 8-10"],
        ["Força", "Flexão na parede", "2 x 6-10"],
        ["Força", "Remada com elástico leve", "2 x 8-10"],
        ["Equilíbrio", "Transferência de peso com apoio", "2 x 30 s"],
        ["Final", "Mobilidade leve", "3-5 min"],
    ];

    $treinoIdoFm = [
        ["Aquecimento", "Marcha no lugar com apoio e mobilidade de ombros", "5 min"],
        ["Força funcional", "Sentar e levantar da cadeira", "2-3 x 6-10"],
        ["Força", "Flexão na parede", "2-3 x 6-10"],
        ["Força", "Remada com elástico leve", "2-3 x 8-10"],
        ["Força", "Elevação de panturrilha segurando apoio", "2 x 10-12"],
        ["Equilíbrio", "Apoio semi-tandem junto a superfície estável", "2 x 15-20 s"],
        ["Final", "Respiração e mobilidade leve", "3-5 min"],
    ];

    $treinoIdoSb = [
        ["Aquecimento", "Marcha sentada/em pé com apoio", "5 min"],
        ["Força funcional", "Sentar e levantar da cadeira", "2 x 6-10"],
        ["Força", "Flexão na parede", "2 x 6-10"],
        ["Mobilidade", "Elevação alternada de joelhos com apoio", "2 x 8 por lado"],
        ["Equilíbrio", "Transferência de peso e passos laterais com apoio", "2 x 8 por lado"],
        ["Cardio leve", "Caminhada confortável", "8-15 min"],
        ["Final", "Alongamento leve", "3 min"],
    ];

    return [
        "ADO-PG" => $treinoAdoPg,
        "ADO-PG-SOB" => $treinoAdoPgSobObe, // tabela própria para este IMC no documento
        "ADO-PG-OBE" => $treinoAdoPgSobObe, // tabela própria para este IMC no documento
        "ADO-GP" => $treinoAdoGp,
        "ADO-GM" => $treinoAdoGm,
        "ADO-SB" => $treinoAdoSb,
        "ADU-PG" => $treinoAduPg,
        "ADU-PG-SOB" => $treinoAduPgSobObe, // tabela própria para este IMC no documento
        "ADU-PG-OBE" => $treinoAduPgSobObe, // tabela própria para este IMC no documento
        "ADU-GP" => $treinoAduGp,
        "ADU-GM" => $treinoAduGm,
        "ADU-SB" => $treinoAduSb,
        "IDO-PG" => $treinoIdoPg,
        "IDO-GP" => $treinoIdoGp,
        "IDO-FM" => $treinoIdoFm,
        "IDO-SB" => $treinoIdoSb,
    ];
}

// Cardápio-modelo de cada dieta-base: [momento, refeição]. Mesma origem e
// mesmo formato de chave de geradorTreinos().
function geradorCardapios(){
    $cardapioAdoPg = [
        ["Café da manhã", "Fruta + aveia ou pão/tapioca + fonte de proteína (ex.: ovo, iogurte natural ou alternativa vegetal)."],
        ["Lanche", "Fruta inteira; em dias de mais fome, combinar com iogurte natural ou alternativa compatível."],
        ["Almoço", "Metade do prato com verduras/legumes + arroz, batata ou outro carboidrato + feijão/lentilha + proteína + água."],
        ["Lanche da tarde", "Fruta, milho cozido, pipoca caseira ou sanduíche simples com recheio proteico."],
        ["Jantar", "Refeição semelhante ao almoço em porção confortável ou sopa caseira com legumes, leguminosa e proteína."],
        ["Ceia opcional", "Fruta ou leite/iogurte compatível, apenas se houver fome."],
    ];

    $cardapioAdoGp = [
        ["Café da manhã", "Pão, tapioca ou aveia + fruta + fonte proteica + complemento energético simples, como pasta de amendoim/castanha apenas quando permitido."],
        ["Lanche da manhã", "Vitamina de fruta com leite ou bebida vegetal compatível; alternativa: fruta + pão/bolo caseiro simples."],
        ["Almoço", "Arroz, macarrão ou tubérculo + feijão/lentilha + proteína + legumes/verduras + azeite ou abacate quando compatível."],
        ["Lanche da tarde", "Sanduíche caseiro com recheio proteico ou iogurte/alternativa vegetal com fruta e aveia."],
        ["Jantar", "Refeição completa semelhante ao almoço."],
        ["Ceia", "Fruta com iogurte/alternativa vegetal, mingau de aveia ou sanduíche simples, conforme fome."],
    ];

    $cardapioAdoGm = [
        ["Café da manhã", "Aveia ou pão/tapioca + fruta + fonte proteica (ovo, leite/iogurte compatível, tofu, feijão em preparação salgada ou alternativa vegetal)."],
        ["Lanche", "Fruta + alimento proteico compatível ou sanduíche caseiro pequeno."],
        ["Almoço", "Arroz, macarrão ou tubérculo + feijão/lentilha/grão-de-bico + proteína + verduras/legumes."],
        ["Lanche próximo ao treino", "Fruta + pão/tapioca/aveia; combinar com fonte proteica se houver tolerância e disponibilidade."],
        ["Jantar", "Refeição completa semelhante ao almoço ou omelete/alternativa vegetal com legumes e carboidrato."],
        ["Ceia opcional", "Iogurte/alternativa vegetal com fruta, leite compatível ou mingau de aveia, conforme fome."],
    ];

    $cardapioAdoSb = [
        ["Café da manhã", "Fruta + aveia/pão/tapioca + fonte proteica compatível."],
        ["Lanche", "Fruta, iogurte/alternativa vegetal, castanhas apenas quando permitidas ou sanduíche simples."],
        ["Almoço", "Metade do prato com verduras/legumes + arroz/tubérculo + feijão/lentilha + proteína."],
        ["Lanche da tarde", "Fruta + alimento simples caseiro, como pão, milho, tapioca ou iogurte compatível."],
        ["Jantar", "Refeição semelhante ao almoço ou sopa caseira completa."],
        ["Ceia opcional", "Apenas se houver fome: fruta, leite/iogurte compatível ou mingau simples."],
    ];

    $cardapioAduPg = [
        ["Café da manhã", "Fruta + aveia ou pão/tapioca + fonte de proteína (ex.: ovo, iogurte natural ou alternativa vegetal)."],
        ["Lanche", "Fruta inteira; em dias de mais fome, combinar com iogurte natural ou alternativa compatível."],
        ["Almoço", "Metade do prato com verduras/legumes + arroz, batata ou outro carboidrato + feijão/lentilha + proteína + água."],
        ["Lanche da tarde", "Fruta, milho cozido, pipoca caseira ou sanduíche simples com recheio proteico."],
        ["Jantar", "Refeição semelhante ao almoço em porção confortável ou sopa caseira com legumes, leguminosa e proteína."],
        ["Ceia opcional", "Fruta ou leite/iogurte compatível, apenas se houver fome."],
    ];

    $cardapioAduGp = [
        ["Café da manhã", "Pão, tapioca ou aveia + fruta + fonte proteica + complemento energético simples, como pasta de amendoim/castanha apenas quando permitido."],
        ["Lanche da manhã", "Vitamina de fruta com leite ou bebida vegetal compatível; alternativa: fruta + pão/bolo caseiro simples."],
        ["Almoço", "Arroz, macarrão ou tubérculo + feijão/lentilha + proteína + legumes/verduras + azeite ou abacate quando compatível."],
        ["Lanche da tarde", "Sanduíche caseiro com recheio proteico ou iogurte/alternativa vegetal com fruta e aveia."],
        ["Jantar", "Refeição completa semelhante ao almoço."],
        ["Ceia", "Fruta com iogurte/alternativa vegetal, mingau de aveia ou sanduíche simples, conforme fome."],
    ];

    $cardapioAduGm = [
        ["Café da manhã", "Aveia ou pão/tapioca + fruta + fonte proteica (ovo, leite/iogurte compatível, tofu, feijão em preparação salgada ou alternativa vegetal)."],
        ["Lanche", "Fruta + alimento proteico compatível ou sanduíche caseiro pequeno."],
        ["Almoço", "Arroz, macarrão ou tubérculo + feijão/lentilha/grão-de-bico + proteína + verduras/legumes."],
        ["Lanche próximo ao treino", "Fruta + pão/tapioca/aveia; combinar com fonte proteica se houver tolerância e disponibilidade."],
        ["Jantar", "Refeição completa semelhante ao almoço ou omelete/alternativa vegetal com legumes e carboidrato."],
        ["Ceia opcional", "Iogurte/alternativa vegetal com fruta, leite compatível ou mingau de aveia, conforme fome."],
    ];

    $cardapioAduSb = [
        ["Café da manhã", "Fruta + aveia/pão/tapioca + fonte proteica compatível."],
        ["Lanche", "Fruta, iogurte/alternativa vegetal, castanhas apenas quando permitidas ou sanduíche simples."],
        ["Almoço", "Metade do prato com verduras/legumes + arroz/tubérculo + feijão/lentilha + proteína."],
        ["Lanche da tarde", "Fruta + alimento simples caseiro, como pão, milho, tapioca ou iogurte compatível."],
        ["Jantar", "Refeição semelhante ao almoço ou sopa caseira completa."],
        ["Ceia opcional", "Apenas se houver fome: fruta, leite/iogurte compatível ou mingau simples."],
    ];

    $cardapioIdoPg = [
        ["Café da manhã", "Fruta + aveia ou pão/tapioca + fonte de proteína (ex.: ovo, iogurte natural ou alternativa vegetal)."],
        ["Lanche", "Fruta inteira; em dias de mais fome, combinar com iogurte natural ou alternativa compatível."],
        ["Almoço", "Metade do prato com verduras/legumes + arroz, batata ou outro carboidrato + feijão/lentilha + proteína + água."],
        ["Lanche da tarde", "Fruta, milho cozido, pipoca caseira ou sanduíche simples com recheio proteico."],
        ["Jantar", "Refeição semelhante ao almoço em porção confortável ou sopa caseira com legumes, leguminosa e proteína."],
        ["Ceia opcional", "Fruta ou leite/iogurte compatível, apenas se houver fome."],
    ];

    $cardapioIdoGp = [
        ["Café da manhã", "Pão, tapioca ou aveia + fruta + fonte proteica + complemento energético simples, como pasta de amendoim/castanha apenas quando permitido."],
        ["Lanche da manhã", "Vitamina de fruta com leite ou bebida vegetal compatível; alternativa: fruta + pão/bolo caseiro simples."],
        ["Almoço", "Arroz, macarrão ou tubérculo + feijão/lentilha + proteína + legumes/verduras + azeite ou abacate quando compatível."],
        ["Lanche da tarde", "Sanduíche caseiro com recheio proteico ou iogurte/alternativa vegetal com fruta e aveia."],
        ["Jantar", "Refeição completa semelhante ao almoço."],
        ["Ceia", "Fruta com iogurte/alternativa vegetal, mingau de aveia ou sanduíche simples, conforme fome."],
    ];

    $cardapioIdoFm = [
        ["Café da manhã", "Aveia ou pão/tapioca + fruta + fonte proteica (ovo, leite/iogurte compatível, tofu, feijão em preparação salgada ou alternativa vegetal)."],
        ["Lanche", "Fruta + alimento proteico compatível ou sanduíche caseiro pequeno."],
        ["Almoço", "Arroz, macarrão ou tubérculo + feijão/lentilha/grão-de-bico + proteína + verduras/legumes."],
        ["Lanche próximo ao treino", "Fruta + pão/tapioca/aveia; combinar com fonte proteica se houver tolerância e disponibilidade."],
        ["Jantar", "Refeição completa semelhante ao almoço ou omelete/alternativa vegetal com legumes e carboidrato."],
        ["Ceia opcional", "Iogurte/alternativa vegetal com fruta, leite compatível ou mingau de aveia, conforme fome."],
    ];

    $cardapioIdoSb = [
        ["Café da manhã", "Fruta + aveia/pão/tapioca + fonte proteica compatível."],
        ["Lanche", "Fruta, iogurte/alternativa vegetal, castanhas apenas quando permitidas ou sanduíche simples."],
        ["Almoço", "Metade do prato com verduras/legumes + arroz/tubérculo + feijão/lentilha + proteína."],
        ["Lanche da tarde", "Fruta + alimento simples caseiro, como pão, milho, tapioca ou iogurte compatível."],
        ["Jantar", "Refeição semelhante ao almoço ou sopa caseira completa."],
        ["Ceia opcional", "Apenas se houver fome: fruta, leite/iogurte compatível ou mingau simples."],
    ];

    return [
        "ADO-PG" => $cardapioAdoPg,
        "ADO-GP" => $cardapioAdoGp,
        "ADO-GM" => $cardapioAdoGm,
        "ADO-SB" => $cardapioAdoSb,
        "ADU-PG" => $cardapioAduPg,
        "ADU-GP" => $cardapioAduGp,
        "ADU-GM" => $cardapioAduGm,
        "ADU-SB" => $cardapioAduSb,
        "IDO-PG" => $cardapioIdoPg,
        "IDO-GP" => $cardapioIdoGp,
        "IDO-FM" => $cardapioIdoFm,
        "IDO-SB" => $cardapioIdoSb,
    ];
}

// Tabela (treino ou cardápio) da combinação; lista vazia se não houver.
function geradorBuscarTabela($tabelas, $grupoIdade, $codObjetivo, $codImc){
    return $tabelas["{$grupoIdade}-{$codObjetivo}-{$codImc}"] ?? $tabelas["{$grupoIdade}-{$codObjetivo}"] ?? [];
}

// Busca o plano-base pela combinação grupo+objetivo+categoria de IMC.
// Retorna null se a combinação não existir no catálogo (ex.: código de
// objetivo incompatível com o grupo de idade).
function geradorBuscarPlano($grupoIdade, $codObjetivo, $codImc){
    $catalogo = geradorCatalogo();
    $grupo = $catalogo[$grupoIdade][$codObjetivo] ?? null;
    if(!$grupo || !isset($grupo["itens"][$codImc])) return null;

    $item = $grupo["itens"][$codImc];

    return [
        "codigo_dieta"  => "D-{$grupoIdade}-{$codObjetivo}-{$codImc}{$item['sufixo']}",
        "codigo_treino" => "T-{$grupoIdade}-{$codObjetivo}-{$codImc}{$item['sufixo']}",
        "status"        => $item["status"],
        "dieta_base"    => $grupo["dieta_base"],
        "observacao"    => trim($item["observacao"] . " " . $grupo["nota"]),
        "treino_base"   => $grupo["treino_base"],
        "orientacao"    => $grupo["orientacao"],
        "exercicios"    => geradorBuscarTabela(geradorTreinos(), $grupoIdade, $codObjetivo, $codImc),
        "refeicoes"     => geradorBuscarTabela(geradorCardapios(), $grupoIdade, $codObjetivo, $codImc),
    ];
}

// Ações aplicadas sobre a dieta-base por exceção alimentar selecionada.
function geradorFiltrosAlimentares(){
    return [
        "Leite e derivados"     => "Substituir leite, iogurte e queijos por opções sem leite/derivados e revisar receitas com esse ingrediente.",
        "Ovos"                  => "Remover ovos e preparações que dependam de ovos.",
        "Amendoim e oleaginosas"=> "Remover amendoim, castanhas, nozes e preparações relacionadas.",
        "Frutos do mar"         => "Remover peixes, crustáceos e moluscos, substituindo por outra fonte de proteína.",
        "Soja"                  => "Remover soja e derivados (tofu, bebida de soja, proteína de soja).",
        "Trigo"                 => "Remover pão, massas e produtos com trigo, priorizando arroz, milho, mandioca, batata e aveia certificada.",
        "Lactose"                => "Trocar por versões sem lactose ou alternativas vegetais compatíveis.",
        "Glúten"                 => "Aplicar apenas opções sem glúten no plano.",
        "Vegetariano"            => "Retirar carnes, aves, peixes e frutos do mar do plano.",
        "Vegano"                 => "Retirar todos os alimentos de origem animal do plano.",
        "Diabetes"               => "Manter refeições regulares com alimentos minimamente processados; o sistema não calcula doses nem substitui acompanhamento médico.",
        "Hipertensão"            => "Priorizar preparações caseiras, com menos sal e ultraprocessados.",
        "Doença renal"           => "Personalização bloqueada para este caso: siga apenas a orientação geral e busque acompanhamento profissional.",
    ];
}

// Ações aplicadas sobre o treino-base por limitação física selecionada.
function geradorFiltrosFisicos(){
    return [
        "Joelho"                    => "Evitar saltos, corrida e agachamento profundo; priorizar sentar/levantar de cadeira, ponte de glúteos e caminhada curta sem dor.",
        "Coluna/lombar"             => "Evitar flexões repetidas do tronco e cargas altas; priorizar bird-dog curto, ponte de glúteos e mobilidade leve.",
        "Ombro"                     => "Evitar flexões e elevações acima da cabeça; priorizar exercícios de membros inferiores e mobilidade leve.",
        "Quadril"                   => "Evitar avanços, agachamentos e subida de degrau com dor; priorizar mobilidade suave e exercícios sentados ou com apoio.",
        "Tornozelo/pé"              => "Evitar saltos e deslocamentos rápidos; priorizar exercícios sentados, ponte de glúteos e caminhada curta se confortável.",
        "Dificuldade de mobilidade" => "Priorizar versões sentadas ou com apoio (parede/cadeira) e tempos mais curtos.",
        "Dificuldade de equilíbrio" => "Priorizar transferência de peso, passos laterais e exercícios com apoio próximo.",
        "Recuperação pós-lesão"     => "Treino de baixa intensidade, 1 a 2 séries, com marcha leve, mobilidade e força básica sem dor.",
    ];
}

// Retorna os ajustes de dieta a exibir e se a personalização foi bloqueada
// (caso de doença renal, por segurança).
function geradorAplicarFiltrosAlimentares(array $itensSelecionados){
    $acoes = geradorFiltrosAlimentares();
    $ajustes = [];
    $bloqueado = false;

    foreach($itensSelecionados as $item){
        if(isset($acoes[$item])){
            $ajustes[] = $acoes[$item];
        }
        if($item === "Doença renal"){
            $bloqueado = true;
        }
    }

    return ["ajustes" => $ajustes, "bloqueado" => $bloqueado];
}

function geradorAplicarFiltrosFisicos(array $itensSelecionados){
    $acoes = geradorFiltrosFisicos();
    $ajustes = [];

    foreach($itensSelecionados as $item){
        if(isset($acoes[$item])){
            $ajustes[] = $acoes[$item];
        }
    }

    return ["ajustes" => $ajustes];
}
