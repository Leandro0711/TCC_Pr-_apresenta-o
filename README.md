# HealthCore (TCC)

Sistema PHP + MySQL de acompanhamento de saúde: cadastro/login, cálculo de
IMC e gerador de dieta e treino por código padronizado.

## Estrutura

```
HealthCore/
├── .env.example          Modelo das variáveis de ambiente (sem segredos)
├── .gitignore             Impede que o .env real vá para o Git
├── index.php              Página de apresentação
├── login.php / cadastro.php / logout.php / esqueci_senha.php / excluir_conta.php
├── home.php               Formulário de perfil + resultado (IMC, dieta, treino)
├── style.css
└── services/
    ├── conn.php               Conexão com o MySQL (mysqli)
    ├── env.php                Carrega o .env para variáveis de ambiente
    ├── UsuarioRepository.php  CRUD de Usuario
    ├── ClienteRepository.php  CRUD de Cliente + objetivo
    ├── ImcRepository.php      Cálculo/classificação/histórico de IMC
    ├── ExcecaoRepository.php  CRUD de excecao_alimentar (checkboxes predefinidos)
    └── GeradorService.php     Catálogo de dietas/treinos e filtros de exceção
```

## Banco de dados

Rodar `bd_tcc_atualizado.sql` (cria o banco `tcc` e as tabelas). Esse
script substitui o `bd_tcc.txt` original: os objetivos cadastrados e as
faixas de IMC de idosos foram ajustados para bater com o gerador de
dieta/treino (ver comentários no próprio arquivo .sql).

## Configuração do e-mail (Gmail SMTP + PHPMailer) — variáveis de ambiente

O envio do código de recuperação de senha usa o **PHPMailer** (em
`services/PHPMailer/`) conectando via SMTP na conta do Gmail. Isso exige uma
**senha de app do Gmail** (não é a senha normal da conta) que **nunca deve
ficar escrita dentro do código-fonte**. Se ela ficar, qualquer pessoa que veja
o arquivo (inclusive no histórico do Git, mesmo que você apague a linha
depois) consegue usar essa credencial para mandar e-mail em nome da conta.

A solução padrão da indústria é guardar esse tipo de segredo fora do código,
em variáveis de ambiente. Veja como isso funciona aqui:

1. **`.env`** — um arquivo de texto simples, no formato `CHAVE=valor`, que fica
   na raiz do projeto (mesma pasta do `index.php`). Ele guarda os segredos
   reais. Esse arquivo **não existe no repositório** — cada pessoa cria o seu
   localmente.
2. **`.env.example`** — uma cópia do `.env` sem os valores reais, só para
   mostrar quais variáveis o projeto precisa. Esse arquivo *pode* ir pro Git,
   já que não tem segredo nenhum dentro.
3. **`.gitignore`** — um arquivo que diz ao Git quais arquivos ele deve
   ignorar (nunca versionar). A linha `.env` que colocamos nele garante que,
   mesmo que alguém rode `git add .`, o `.env` real nunca é enviado ao GitHub.
4. **`services/env.php`** — um pequeno script que lê o `.env` na hora que o
   PHP inicia e disponibiliza os valores via `getenv("NOME_DA_VARIAVEL")`,
   em vez de a gente escrever o valor direto no código.

### Como configurar na sua máquina

1. Copie `.env.example` e renomeie a cópia para `.env`.
2. Em `GMAIL_USER`, coloque o e-mail Gmail que vai enviar os códigos.
3. Gere uma **senha de app** em
   https://myaccount.google.com/apppasswords (precisa da verificação em duas
   etapas ativada na conta) e cole o valor de 16 caracteres em
   `GMAIL_APP_PASSWORD`, substituindo o placeholder
   `coloque_a_senha_de_app_aqui`.
4. Pronto — o `esqueci_senha.php` já lê essas variáveis automaticamente. Se o
   `.env` não existir ou as variáveis estiverem vazias, o envio de e-mail
   simplesmente falha de forma controlada (sem quebrar o site), em vez de
   expor um segredo no código. Se as variáveis estiverem preenchidas mas
   incorretas (placeholder, senha normal em vez de senha de app, etc.), o erro
   exato do PHPMailer é gravado no log de erros do PHP (não é mostrado ao
   usuário, por segurança).

Essa mesma ideia (segredo fora do código, em variável de ambiente) vale para
qualquer senha, chave de API ou token que o projeto venha a usar no futuro —
não só para o e-mail.

## Gerador de dieta e treino

Segue a lógica dos documentos de planejamento do TCC: idade define o
grupo (Adolescente/Adulto/Idoso), IMC é classificado por faixa (tabela
`classificacao_imc` para Adulto/Idoso; curva aproximada por idade para
Adolescente), e a combinação grupo+objetivo+IMC busca um plano no
catálogo de 44 combinações em `GeradorService::geradorCatalogo()`.
Perfis com objetivo incompatível com o IMC (ex.: emagrecer com baixo
peso) caem na versão protegida do plano e exibem aviso de orientação
profissional. Exceções alimentares e físicas (checkboxes do formulário)
são aplicadas por cima do plano-base.
"# Tcc-Final" 
"# TCC_Pr-_apresenta-o" 
