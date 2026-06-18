# Arquitetura — Algorithmia

[← Inicio](README.md)

## Visao geral

Algorithmia e um PHP MVC artesanal — sem Composer, sem framework. Uma unica
URL de entrada, um roteador simples, controllers que delegam para services e
models, e views PHP puras.

## Fluxo de uma requisicao

```
Navegador
  |
  | GET index.php?url=batalha/responder
  v
index.php          (bootstrap: sessao, headers, autoload, config)
  |
  v
Router::despachar()
  |
  | Resolve "batalha" -> BatalhaController, "responder" -> metodo
  | Bloqueia metodos herdados de Controller via ReflectionMethod
  v
BatalhaController::responder()
  |
  |-- Auth::exigirLogin() / exigirPersonagem()
  |-- Ler corpo JSON via $this->corpoJson()
  |-- BatalhaService::verificar(estado, resposta)
  |      |-- Desafio (Model) -- consultas ao banco
  |      |-- RespostaLog (Model) -- registro de resposta
  |      |-- ReputacaoService -- ajuste de reputacao
  |      |-- ProgressaoService -- XP, nivel, HP/MP
  |
  |-- $this->json($dados)  OU  $this->view('batalha/tela', $data)
  v
View PHP pura (app/views/)
  |
  | helpers: e(), url(), asset(), svg(), barra()
  v
Resposta HTML/JSON
```

## Bootstrap (`index.php`)

1. `session_set_cookie_params` — HttpOnly, SameSite=Lax, Secure condicional.
2. `session_start()`.
3. Headers de segurança globais: `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`.
4. `require config/db.php` — define `getConnection()` (singleton PDO).
5. `require config/config.php` — constantes (`BASE_URL`, `NOME_JOGO`, `CLASSES`, `DESAFIOS_POR_BATALHA`, etc.).
6. `require config/bestiario.php` — constante `BESTIARIO` com lore de cada inimigo.
7. `require app/core/helpers.php` — funcoes globais (`e`, `url`, `asset`, `svg`, `csrf_field`, etc.).
8. `spl_autoload_register` — carrega automaticamente de `app/core/`, `app/models/`, `app/services/`.
9. `(new Router())->despachar($_GET['url'] ?? '')`.

> Controllers sao carregados pelo Router sob demanda (nao entram no autoload).

## Camada PDO (`config/db.php`)

- Funcao `getConnection(bool $semBanco = false): PDO` — singleton por requisicao.
- `PDO::ERRMODE_EXCEPTION`, `FETCH_ASSOC`, `EMULATE_PREPARES = false` (sem SQL injection via emulacao).
- Credenciais via variaveis de ambiente (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`);
  fallback para defaults de desenvolvimento.
- Override local sem versionar: `config/db.local.php` (chamado automaticamente se existir).

## Nucleo (`app/core/`)

| Arquivo | Responsabilidade |
|---|---|
| `Router.php` | Mapeia `?url=controller/metodo/param` para classe+metodo. Usa `ReflectionMethod` para bloquear metodos herdados e protected. |
| `Controller.php` | Base dos controllers: `view()`, `redirect()`, `json()`, `flash()`, `corpoJson()`, `exigirCsrf()`. Metodos sao protegidos — nao roteados. |
| `Model.php` | CRUD generico: `findAll`, `findById`, `findBy`, `where`, `create`, `update`, `delete`, `count`. `colunaSegura()` valida nomes de coluna antes de interpolacao. |
| `Auth.php` | Autenticacao stateless-via-sessao. `exigirLogin()`, `exigirPersonagem()`, `exigirMestre()`. Memoizacao por requisicao para evitar SELECTs repetidos. |
| `helpers.php` | `e()` (XSS), `url()`, `asset()`, `assetV()` (cache-busting), `svg()`, `svgSlug()`, `svgAtor()`, `marcaHtml()`, `csrf_field()`, `csrf_valido()`, `barra()`, `rotuloReputacao()`. |

## Models (`app/models/`)

Herdam de `Model` e definem `$table` + metodos especificos:

| Model | Tabela | Nota |
|---|---|---|
| `Usuario` | `usuarios` | Autenticacao; `papel` = aluno/mestre |
| `Personagem` | `personagens` | HP/MP/XP/ouro/nivel/reputacao/classe |
| `Fase` | `fases` | `comMestre()` faz JOIN com mestres |
| `Desafio` | `desafios` | `poolDaFase()` retorna desafios decodificados |
| `ProgressoFase` | `progresso_fases` | `registrar()` usa INSERT ... ON DUPLICATE KEY |
| `RespostaLog` | `respostas_log` | Log de cada resposta para anti-repeticao |
| `Inventario` | `inventario` | Quantidade de itens por personagem |
| `Conquista` | `conquistas` | Catalogo de conquistas |
| `Mestre` | `mestres` | Dados dos 5 mestres |
| `Dialogo` | `dialogos` | Cenas narrativas por momento/variante |

## Services (`app/services/`)

Logica de negocio que envolve multiplos models:

| Service | Responsabilidade |
|---|---|
| `BatalhaService` | Maquina de estados da batalha (sessao). Sorteio anti-repeticao, verificacao de resposta, combate, recompensas. Gabarito nunca vai ao cliente (`estadoPublico()`). |
| `ProgressaoService` | Ganho de XP, calculo de nivel (curva `100*(n-1)^1.5`), atualizacao de HP/MP max. |
| `ReputacaoService` | Ajuste de reputacao (-100..+100) por acao. |
| `ConquistaService` | Avaliar e conceder conquistas apos cada fase. |

## Views (`app/views/`)

- PHP puro; variavel passada via `extract($data, EXTR_SKIP)` pelo `Controller::view()`.
- Layout: `app/views/layout/header.php` + view + `app/views/layout/footer.php`.
- Saida sempre via `e()` — nunca echo/print de dados brutos.
- Paginas que nao precisam de layout: `['_semLayout' => true]` em `$data`.

## Responsabilidades por pasta

```
index.php              Bootstrap + dispatch
config/
  config.php           Constantes de jogo (classes, XP, combate, reputacao, regioes)
  db.php               PDO singleton
  bestiario.php        Lore dos inimigos (BESTIARIO[])
app/
  core/                Nucleo MVC (nao editar sem entender o impacto global)
  controllers/         Um arquivo por area de tela
  models/              Um arquivo por tabela
  services/            Logica de negocio multi-model
  views/               Templates PHP por area
database/
  schema.sql           Schema completo (CREATE IF NOT EXISTS)
  migrate.php          Migrador CLI idempotente
  migrations/          Alteracoes incrementais (AAAAMMDD-descricao.sql)
  seeds.sql            Dados iniciais (mestres, fases, itens, conquistas)
  seed-banco-questoes.php  Insercao idempotente do pool de desafios
  banco-questoes/      Um .php por materia retornando lista de perguntas
public/
  css/                 style.css + batalha.css + cena.css + mapa.css
  img/                 Assets servidos (webp preferido, png fallback)
  js/                  batalha.js (logica AJAX do combate)
tools/                 Scripts Python: PDFs, rembg, galeria
docs/                  Documentacao (evolucao-visual, auditoria, materias, codex)
```

## Diagrama de dependencia simplificado

```
index.php
  config/ ──────────────────────> constantes globais
  app/core/helpers.php ──────────> funcoes globais
  Router
    └─> Controller (base)
          └─> Auth ──────────────> Usuario, Personagem (Models)
          └─> BatalhaController
                └─> BatalhaService
                      └─> Desafio, Personagem, Inventario, RespostaLog (Models)
                      └─> ReputacaoService, ProgressaoService (Services)
                      └─> ConquistaService (Service)
```
