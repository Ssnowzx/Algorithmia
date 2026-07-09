# Inventário Técnico do Domínio — Algorithmia

> Base para o port **PHP puro (MVC artesanal, PDO/MySQL) → Laravel 13 + PostgreSQL**.
> Fonte: leitura do código (não do README). Onde algo está só documentado nos `.md`
> e **não** no código, está marcado `[SÓ DOC]`. Citações no formato `arquivo.php:linha`.

---

## 0. Como o roteamento funciona (contexto)

- Front controller único: `index.php:45-46` lê `$_GET['url']` e chama `Router::despachar`.
- `app/core/Router.php:8-39`: `?url=controller/metodo/p1/p2` → `UcfirstController::metodo(p1,p2)`.
  Sem controller ⇒ `HomeController`; sem método ⇒ `index`.
- **Não há verificação de verbo HTTP no roteador.** Qualquer rota aceita GET, POST, etc.
  O verbo só é (às vezes) checado dentro do controller/método.
- Só roteia métodos **públicos, não-estáticos e não herdados** de `Controller`
  (`Router.php:45-54`) — utilidades como `view/redirect/json` não viram rota.
- Autoload: núcleo/models/services por `spl_autoload_register` (`index.php:35-43`);
  controllers via `require` no Router.

### Semântica de CSRF (ler antes da tabela de rotas)
Há **duas** guardas, em `app/core/Controller.php`:

- `exigirCsrf()` (`Controller.php:74-80`): exige `REQUEST_METHOD === 'POST'` **E**
  `csrf_valido()`. Ou seja, **hoje bloqueia GET** (retorna 419). O comentário
  confirma que isso foi corrigido: *"Bloqueia CSRF via GET (antes só validava o POST;
  um GET passava direto)."* → **rotas que chamam `exigirCsrf()` NÃO são GET-acessíveis.**
- `exigirCsrfAjax()` (`Controller.php:86-92`): valida só o header `X-CSRF-Token`
  (`hash_equals`). **NÃO checa o verbo HTTP.** Logo aceita GET/POST/qualquer verbo,
  desde que o token venha no header. Como um header custom cross-site exige preflight
  CORS, isso protege contra CSRF de terceiros, mas **não força POST**.

`csrf_valido()` (`helpers.php:489-493`) só olha `$_POST['csrf']` — não cobre AJAX
(por isso a variante AJAX lê o header). Token gerado em `csrf_token()` (`helpers.php:473-479`).

**Rotas de ESCRITA acessíveis via GET sem qualquer CSRF** (não chamam nenhuma guarda):
1. `historia/concluir/{id}` — **grava no banco** (progresso + XP). ⚠️ risco real.
2. `historia/final` — grava conquista de final (idempotente, `INSERT IGNORE`).
3. `auth/logout` — destrói a sessão (logout-CSRF; sem impacto em dados).

Escrita GET **por design** (não é bug, mas muda estado de sessão):
- `batalha/iniciar/{id}` — monta e grava `$_SESSION['batalha']`.

AJAX que **não força POST** mas exige token no header (CSRF-seguro, verbo livre):
`batalha/responder`, `batalha/fragmento`, `batalha/especial`, `batalha/pocao`, `batalha/fugir`.

---

## 1. Mapa de rotas

Legenda: **Auth** = exige login/personagem; **CSRF** = `POST`=exigirCsrf (bloqueia GET),
`AJAX`=exigirCsrfAjax (header, verbo livre), `—`=nenhuma; **Escr.** = escreve estado
(BD=banco, SESS=sessão).

| Rota `?url=` | Controller::método | Verbo usado hoje | Auth | CSRF | Escr. | Observações |
|---|---|---|---|---|---|---|
| `` / `home/index` | `HomeController::index` | GET | não | — | — | Redireciona logado→mapa/criar (`HomeController.php:7-22`) |
| `auth/login` | `AuthController::login` | GET+POST | não | POST (`:18`) | SESS (login) | GET renderiza; POST autentica |
| `auth/registro` | `AuthController::registro` | GET+POST | não | POST (`:40`) | **BD** | Cria `usuarios` (`:62-67`) |
| `auth/logout` | `AuthController::logout` | **GET** | não | **—** | SESS | ⚠️ GET destrói sessão (`:77-81`) |
| `auth/criarPersonagem` | `AuthController::criarPersonagem` | GET+POST | login | POST (`:95`) | **BD** | Cria `personagens`+itens iniciais (`:118-149`) |
| `batalha/iniciar/{faseId}` | `BatalhaController::iniciar` | GET | personagem | — | **SESS** | Grava `$_SESSION['batalha']` (`:19-65`) |
| `batalha/responder` | `BatalhaController::responder` | POST(AJAX) | personagem | AJAX (`:72`) | **BD** | Verbo não forçado; grava log/HP/recompensa |
| `batalha/fragmento` | `BatalhaController::fragmento` | POST(AJAX) | personagem | AJAX (`:87`) | **BD** | Consome item + reputação |
| `batalha/especial` | `BatalhaController::especial` | POST(AJAX) | personagem | AJAX (`:101`) | **BD** | Gasta MP (update personagem) |
| `batalha/pocao` | `BatalhaController::pocao` | POST(AJAX) | personagem | AJAX (`:111`) | **BD** | Consome poção |
| `batalha/fugir` | `BatalhaController::fugir` | POST(AJAX) | não* | AJAX (`:123`) | SESS | Só limpa `$_SESSION['batalha']` (*não chama exigirPersonagem) |
| `historia/lore` | `HistoriaController::lore` | GET | **não** | — | — | Vitrine pública (`:13-19`) |
| `historia/ver/{faseId}` | `HistoriaController::ver` | GET | personagem | — | — | Só leitura/render diálogo |
| `historia/concluir/{faseId}` | `HistoriaController::concluir` | **GET** | personagem | **—** | **BD** | ⚠️ Escrita GET sem CSRF: progresso+XP (`:61-82`) |
| `historia/final` | `HistoriaController::final` | GET | personagem | **—** | **BD** | Concede conquista de final (idempotente) (`:87-108`) |
| `historia/escolherFinal` | `HistoriaController::escolherFinal` | POST | personagem | POST (`:116`) | **BD** | Grava `escolhas`+conquista |
| `mapa/index` | `MapaController::index` | GET | personagem | — | — | Só leitura |
| `inventario/index` | `InventarioController::index` | GET | personagem | — | — | Só leitura |
| `inventario/equipar/{itemId}` | `InventarioController::equipar` | POST | personagem | POST (`:25`) | **BD** | Equipa/desequipa; concede objetivos |
| `inventario/desequipar/{itemId}` | `InventarioController::desequipar` | POST | personagem | POST (`:88`) | **BD** | |
| `inventario/usar/{itemId}` | `InventarioController::usar` | POST | personagem | POST (`:103`) | **BD** | Poção fora de batalha |
| `inventario/descartar/{itemId}` | `InventarioController::descartar` | POST | personagem | POST (`:126`) | **BD** | |
| `loja/index` | `LojaController::index` | GET | personagem | — | — | Só leitura |
| `loja/comprar/{itemId}` | `LojaController::comprar` | POST | personagem | POST (`:39`) | **BD** | Debita ouro, adiciona item |
| `loja/vender/{itemId}` | `LojaController::vender` | POST | personagem | POST (`:61`) | **BD** | Metade do preço; Fragmento não vende |
| `perfil/index` | `PerfilController::index` | GET | personagem | — | — | Leitura pesada (maestria/missões/regiões/onboarding) |
| `ranking/index` | `RankingController::index` | GET | personagem | — | — | Só leitura |
| `mestre/index` | `MestreController::index` | GET | **mestre** | — | — | `exigirMestre` no construtor (`:8-11`) |
| `mestre/desafios` | `MestreController::desafios` | GET | mestre | — | — | |
| `mestre/novoDesafio` | `MestreController::novoDesafio` | GET | mestre | — | — | |
| `mestre/editarDesafio/{id}` | `MestreController::editarDesafio` | GET | mestre | — | — | |
| `mestre/salvarDesafio` | `MestreController::salvarDesafio` | POST | mestre | POST (`:63`) | **BD** | Valida por tipo (`:92-150`) |
| `mestre/excluirDesafio/{id}` | `MestreController::excluirDesafio` | POST | mestre | POST (`:154`) | **BD** | |
| `mestre/fases` | `MestreController::fases` | GET | mestre | — | — | |
| `mestre/novaFase` / `editarFase/{id}` | `MestreController::…` | GET | mestre | — | — | |
| `mestre/salvarFase` | `MestreController::salvarFase` | POST | mestre | POST (`:245`) | **BD** | |
| `mestre/excluirFase/{id}` | `MestreController::excluirFase` | POST | mestre | POST (`:275`) | **BD** | |
| `mestre/itens` / `novoItem` / `editarItem/{id}` | `MestreController::…` | GET | mestre | — | — | |
| `mestre/salvarItem` | `MestreController::salvarItem` | POST | mestre | POST (`:307`) | **BD** | |
| `mestre/excluirItem/{id}` | `MestreController::excluirItem` | POST | mestre | POST (`:339`) | **BD** | |

**Resumo de escrita via GET:** 3 rotas escrevem sem nenhuma CSRF e são GET-acessíveis
(`historia/concluir` = BD; `historia/final` = BD idempotente; `auth/logout` = sessão).
Adicionalmente `batalha/iniciar` escreve sessão por design, e os 5 endpoints AJAX de
batalha não forçam POST (mas exigem token no header).

---

## 2. Tabelas (13) — schema MySQL → tradução PostgreSQL

Schema em `database/schema.sql`. Charset/engine global de **toda** tabela:
`ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci` → no PostgreSQL vira
banco em `UTF8` + `COLLATE` por coluna (ver §6). **Toda** PK é `INT AUTO_INCREMENT` →
`GENERATED ALWAYS AS IDENTITY` ou `SERIAL`/`BIGSERIAL`. Toda coluna `*_em DATETIME
DEFAULT CURRENT_TIMESTAMP` → `TIMESTAMP DEFAULT now()`.

| # | Tabela | Colunas-chave | MySQL-específico a traduzir |
|---|---|---|---|
| 1 | `usuarios` (`:19-26`) | id PK, nome, email UNIQUE, senha_hash, papel, criado_em | `AUTO_INCREMENT`; **ENUM papel** `('jogador','mestre')` default `jogador`; `email UNIQUE` é **case-insensitive** pela collation `_ci` (ver §6) |
| 2 | `mestres` (`:31-43`) | id, nome, titulo, disciplina, regiao, historia/personalidade TEXT, bordao, svg_slug, cor_tema `VARCHAR(7)`, ordem | `AUTO_INCREMENT`; sem ENUM |
| 3 | `itens` (`:48-58`) | id, nome, descricao, tipo, **efeito JSON**, preco, svg_slug, raridade, compravel | `AUTO_INCREMENT`; **ENUM tipo** `('arma','escudo','acessorio','pocao','especial')`; **ENUM raridade** `('comum','raro','epico','lendario')`; `JSON`→`jsonb`; **`compravel TINYINT(1)`** usado como bool (código escreve `1/0`, lê `!$item['compravel']` em `LojaController.php:42`) |
| 4 | `personagens` (`:63-80`) | id, usuario_id UNIQUE FK, nome, classe, nivel, xp, hp_max/atual, mp_max/atual, ouro, reputacao, capitulo, criado_em, **atualizado_em** | `AUTO_INCREMENT`; **ENUM classe** `('mago','guerreiro','ranger','xeno','elfo','draconato')`; **`atualizado_em … ON UPDATE CURRENT_TIMESTAMP`** → PG não tem; usar trigger; FK `ON DELETE CASCADE` |
| 5 | `fases` (`:85-103`) | id, mestre_id FK NULL, ordem_global, nome, tipo, descricao, inimigo_nome/svg/hp/ataque, xp_recompensa, ouro_recompensa, item_drop_id FK NULL, requisito_fase_id **FK auto-referente** NULL | `AUTO_INCREMENT`; **ENUM tipo** `('historia','licao','chefe','chefe_final','secundaria')` default `licao`; 3 FKs `ON DELETE SET NULL` (mestre, item_drop, requisito auto-ref) |
| 6 | `desafios` (`:108-121`) | id, fase_id FK, ordem, tipo, assunto, pergunta TEXT, codigo TEXT, **opcoes JSON**, **resposta JSON NOT NULL**, explicacao TEXT, dificuldade `TINYINT` | `AUTO_INCREMENT`; **ENUM tipo** `('multipla','vf','completar','erro','ordenar','arrastar')`; **ENUM assunto** `('php','mvc','sql','poo','estruturas','redes','logica','calculo')`; `JSON`→`jsonb`; `TINYINT dificuldade`→`smallint`; FK CASCADE |
| 7 | `inventario` (`:126-135`) | id, personagem_id FK, item_id FK, quantidade, **equipado TINYINT(1)**, **UNIQUE(personagem_id,item_id)** | `AUTO_INCREMENT`; `equipado` bool (lê `=== 1`, `= 1` em SQL); UNIQUE usado por `INSERT ON DUPLICATE`→precisa existir no PG p/ `ON CONFLICT`; 2 FKs CASCADE |
| 8 | `progresso_fases` (`:140-152`) | id, personagem_id FK, fase_id FK, estrelas `TINYINT`, acertos, erros, **usou_ia TINYINT(1)**, concluida_em, **UNIQUE(personagem_id,fase_id)** | `AUTO_INCREMENT`; `usou_ia` bool; `estrelas` smallint; UNIQUE usado por upsert; FKs CASCADE |
| 9 | `conquistas` (`:157-164`) | id, codigo UNIQUE, nome, descricao, svg_slug, **secreta TINYINT(1)** | `AUTO_INCREMENT`; `secreta` bool |
| 10 | `conquistas_personagem` (`:169-176`) | personagem_id, conquista_id, obtida_em, **PK composta** | **PK (personagem_id,conquista_id)** — base do `INSERT IGNORE`→`ON CONFLICT DO NOTHING`; FKs CASCADE |
| 11 | `dialogos` (`:181-191`) | id, fase_id FK, momento, variante, ordem, falante, svg_slug, texto TEXT | `AUTO_INCREMENT`; **ENUM momento** `('antes','vitoria','derrota')` default `antes`; **ENUM variante** `('padrao','ia')` default `padrao`; FK CASCADE |
| 12 | `escolhas` (`:196-203`) | id, personagem_id FK, codigo, valor, criado_em | `AUTO_INCREMENT`; sem ENUM; sem UNIQUE (dedupe é feito em PHP: DELETE+INSERT em `Escolha::definir`, `Escolha.php:12-19`) |
| 13 | `respostas_log` (`:208-217`) | id, personagem_id FK, desafio_id FK, **correta TINYINT(1)**, **usou_ia TINYINT(1)**, respondido_em | `AUTO_INCREMENT`; 2 bools; FKs CASCADE |

Tabela extra criada em runtime pelo migrador: `migracoes_aplicadas(arquivo PK, aplicada_em)`
(`migrate.php:152-157`) — controle de migrações caseiro.

### ENUMs a traduzir (total: **9**)
1. `usuarios.papel` — jogador, mestre
2. `itens.tipo` — arma, escudo, acessorio, pocao, especial
3. `itens.raridade` — comum, raro, epico, lendario
4. `personagens.classe` — mago, guerreiro, ranger, xeno, elfo, draconato (`xeno/elfo/draconato` vieram da migração `20250616-novas-classes.sql`)
5. `fases.tipo` — historia, licao, chefe, chefe_final, secundaria
6. `desafios.tipo` — multipla, vf, completar, erro, ordenar, arrastar
7. `desafios.assunto` — php, mvc, sql, poo, estruturas, redes, logica, **calculo** (`calculo` veio de `20250618-assunto-calculo.sql`)
8. `dialogos.momento` — antes, vitoria, derrota
9. `dialogos.variante` — padrao, ia

> No PG: criar `CREATE TYPE … AS ENUM` **ou** usar `VARCHAR + CHECK`. Recomenda-se
> `CHECK` (o Laravel/Eloquent lida melhor; e o código PHP compara strings livremente,
> ex. `$fase['tipo'] === 'chefe_final'`). Note que ENUMs são **estendidos** por migração
> (classe/assunto) — em PG isso é `ALTER TYPE … ADD VALUE` (não transacional) vs. simples
> troca de `CHECK`.

### Construtos MySQL em SQL escrita à mão (fora do schema)
- **`INSERT IGNORE`** — `Conquista::conceder` (`Conquista.php:33-38`) e migração
  `20260624-objetivos-loja.sql`. → `INSERT … ON CONFLICT DO NOTHING`. O código depende de
  `rowCount() > 0` para saber se foi **inédita** (`Conquista.php:38`) — em PG usar
  `RETURNING` + checar se veio linha (`ON CONFLICT DO NOTHING` não conta como affected).
- **`INSERT … ON DUPLICATE KEY UPDATE`** — `ProgressoFase::registrar`
  (`ProgressoFase.php:36-45`), com `GREATEST(estrelas, VALUES(estrelas))`, `VALUES(...)`
  e `concluida_em = NOW()`. → `ON CONFLICT (personagem_id,fase_id) DO UPDATE SET
  estrelas = GREATEST(progresso_fases.estrelas, EXCLUDED.estrelas), … , concluida_em = now()`.
- **`ORDER BY FIELD(i.tipo, …)`** — ordenação do inventário (`Inventario.php:19`).
  PG não tem `FIELD()`; usar `array_position(ARRAY[...], tipo)` ou `CASE`.
- **`YEARWEEK(x, 3)`** (semana ISO) — `ProgressoFase::fasesSemana` (`:87`),
  `RespostaLog::metricasSemana` (`:98`). → `to_char(x,'IYYYIW')` ou
  `(EXTRACT(ISOYEAR FROM x), EXTRACT(WEEK FROM x))`.
- **`NOW() - INTERVAL 7 DAY`** — `ProgressoFase::resumoSemana` (`:72`),
  `RespostaLog::resumoSemana` (`:71`). → `now() - INTERVAL '7 days'`.
- **`SUM(pf.estrelas = 3)`** (bool→int implícito) — `Mestre::progressoPorRegiao`
  (`ProgressoFase`? não; `Mestre.php:32`). PG: `SUM((pf.estrelas = 3)::int)` ou
  `COUNT(*) FILTER (WHERE pf.estrelas = 3)`.
- **`lastInsertId()` sem argumento** — `Model::create` (`Model.php:84`). Em PDO/pgsql
  precisa do nome da sequência **ou** trocar por `INSERT … RETURNING id`. **Vai quebrar**
  no port se copiado tal qual.
- `LIMIT :limite` com `bindValue(PARAM_INT)` — `Personagem::ranking` (`Personagem.php:14-23`):
  OK em ambos.

---

## 3. Regras de negócio (matriz)

Det.=determinística · RNG=usa `shuffle`/`rand` · SESS=depende de `$_SESSION` · BD=depende do banco.

| Regra | Onde vive | Det. | RNG | SESS | BD | Notas |
|---|---|:--:|:--:|:--:|:--:|---|
| **Dano por acerto** | `BatalhaService::calcularDano` (`:435-449`) | sim | não | sim | não | `base=(ataque + nivel*3 + dif*2)`; `mult=combo × especial × fúria`; `DANO_BASE_POR_NIVEL=3` (`config.php:100`) |
| **Combo** | `BatalhaService::responder` (`:264`,`:437`) | sim | não | sim | não | `min(COMBO_MAX=4, combo+1)`; bônus `1+(combo-1)*0.25` (`config.php:101-102`); zera ao errar (`:275`) |
| **Ataque especial** | `armarEspecial` (`:361-378`); consumo em `calcularDano` (`:438`) | sim | não | sim | **sim** | Custa `CUSTO_MP_ESPECIAL=15`; dobra próximo dano (`MULTIPLICADOR_ESPECIAL=2.0`); persiste MP no personagem (`:376`) |
| **Poção (em batalha)** | `usarPocao` (`:383-426`) | sim | não | sim | **sim** | Cura respeita teto HP/MP; remove item do inventário; concede objetivo `primeira_pocao` |
| **Poção (fora de batalha)** | `InventarioController::usar` (`:100-121`) | sim | não | não | **sim** | Cura direto no personagem |
| **Fragmento da IA** | `usarFragmentoIa` (`:330-356`) | sim | não | sim | **sim** | Acerto automático; consome 1 Fragmento; `reputacao += REPUTACAO_USO_IA(-10)`; marca `usou_ia`; **valida batalha ativa antes de gastar** |
| **Morte súbita / fúria** | `responder` (`:298-303`), `multiplicadorFuria` (`:318-324`) | sim | não | sim | não | Ao atingir `total` (limite de ritmo) com ambos vivos: `morte_subita=true`, `rodada_subita++`; fúria `min(4.0, 1 + 0.5*rodada)` (`config.php:111-112`) |
| **Fim de batalha** | `responder` (`:290-308`) | sim | não | sim | **sim** | **Só termina quando um HP zera** (nunca por acabar perguntas). `finalizar` persiste HP/MP (`:562-572`, HP nunca <1) |
| **XP + curva de nível** | `ProgressaoService::ganharXp` (`:24-49`); `xpParaNivel` (`config.php:91-97`) | sim | não | não | **sim** | Curva: `100*(N-1)^1.5`; cada nível `hp_max+=15`, `mp_max+=8`; subir restaura HP/MP totais |
| **Ouro (recompensa)** | `RecompensaService::conceder` (`:34-41`) | sim | não | não | **sim** | `ouro_recompensa`; **Ranger +20%** (`:35-37`) |
| **Reputação** | `ReputacaoService::ajustar` (`:20-26`) | sim | não | não | **sim** | Clamp `[-100,100]` (`config.php:129-130`); `-10` ao usar IA; `+5` ao vencer sem IA (`RecompensaService:66-68`) |
| **Estrelas (1–3)** | `ProgressaoService::calcularEstrelas` (`:81-90`) | sim | não | não | não | usou_ia⇒1; 0 erros⇒3; ≤2 erros⇒2; senão 1 |
| **Desbloqueio de fase** | `ProgressaoService::faseLiberada` (`:55-62`) | sim | não | não | **sim** | Liberada se `requisito_fase_id` nulo ou já concluído (mapa de progresso) |
| **Avanço de capítulo** | `ProgressaoService::atualizarCapitulo` (`:67-76`) | sim | não | não | **sim** | Ao vencer chefe/chefe_final: `capitulo = ordem do mestre` (só se maior) |
| **Conquistas (pós-fase)** | `ConquistaService::avaliarAposFase` (`:62-112`) | sim | não | não | **sim** | primeiro_passo, sem_falhas, cacador_de_chefes, tentacao, arquivista_do_vazio (secundárias **hardcoded [8,14,20,32]** `:86`), aprendiz_veterano(N5)/lenda_viva(N10), colecionador(≥8 itens) |
| **Conquista de região / Puro de Coração** | `RecompensaService::concederConquistaDeRegiao` (`:96-113`), `concederPuroDeCoracao` (`:121-141`) | sim | não | não | **sim** | Região via `REGIOES_MESTRE[svg_slug]` (`config.php:135-141`); Puro = todas licao+chefe da região sem IA |
| **Objetivos de loja** | `ConquistaService::concederObjetivo` (`:40-55`) | sim | não | não | **sim** | Credita ouro `OBJETIVOS_OURO` (`config.php:121-125`) só na 1ª vez (idempotência via conquista) |
| **Maestria por matéria** | `MaestriaService` (`:23-132`) + `MAESTRIA_FAIXAS` (`config.php:162-172`) | sim | não | não | **sim** (lê stats) | **Pura**; faixa por (acertos, precisão sustentada); read-only; sem migration |
| **Missões da semana** | `MissaoService` (`:22-96`) + `MISSOES_SEMANAIS` (`config.php:180-190`) | sim | não | não | **sim** (métricas) | Seleção **determinística por semana ISO** (`indiceSemanaAtual`=`date('o')*53+date('W')`, `:12-14`); read-only, sem recompensa/persistência |
| **Domínio de região** | `RegiaoService` (`:20-115`) + `REGIAO_FAIXAS` (`config.php:197-205`) | sim | não | não | **sim** | Estados a_explorar→em_jornada→conquistada→dominada (3★ em todas licao+chefe); read-only |
| **Onboarding** | `OnboardingService` (`:18-59`) | sim | não | não | **sim** | Passo "herói" sempre feito (endowed progress); some após `ONBOARDING_NIVEL_MAX=3` (`config.php:210`); read-only |
| **Anti-repetição (sorteio)** | `BatalhaService::sortearDesafios` (`:100-135`) | **não** | **SIM** | não | **sim** | Prioriza inéditos via `respostas_log`, `shuffle`, ordena por dificuldade; N por tipo em `DESAFIOS_POR_BATALHA` (`config.php:217-224`) |
| **Reciclagem de desafios (Duelo Final)** | `garantirDesafioAtual` (`:143-156`) | **não** | **SIM** | sim | não | `shuffle` da reserva quando esgota |
| **Verificação de resposta** | `BatalhaService::verificar` (`:507-541`) | sim | não | não | não | Por tipo: índice / bool / texto normalizado / sequência. `normalizar` ignora espaços e `;` final (`:550-556`) |
| **Variante de diálogo** | `ReputacaoService::variante` (`:32-35`) | sim | não | não | não | `reputacao <= -20` ⇒ variante `ia`, senão `padrao` |
| **Final determinado** | `ReputacaoService::finalDeterminado` (`:41-59`) | sim | não | não | **sim** | Combina `escolhas.final` + reputação → mestre/singularidade/equilibrio |

**Fragmentos/itens iniciais** (`AuthController::criarHeroi` `:118-149`): 3× `item-fragmento-ia`
+ 2× `item-pocao-hp`, ouro inicial 50, reputação 0, capítulo 0. Itens resolvidos por
`svg_slug` (não por id) — dependem do seed.

**`bestiario` (`config/bestiario.php`)**: `const BESTIARIO` (23 entradas), **puro dado de
lore** chaveado por `svg_slug` do inimigo. Único uso no código: `views/batalha/arena.php:19`
(exibição). Sem lógica de negócio. No Laravel pode virar config/array PHP igual, ou tabela.

---

## 4. Fluxo de recompensa de batalha — efeitos colaterais e ordem exata

Gatilho: `BatalhaController::finalizarSePreciso` (`:133-149`) chama
`RecompensaService::conceder` **uma vez**, e a idempotência depende do flag de **sessão**
`$_SESSION['batalha']['recompensado']` (`BatalhaService::marcarRecompensado` `:172-177`),
**não** do banco. `responder()` também barra reprocesso via `$estado['finalizada']` (`:239`).

Ordem das escritas em `RecompensaService::conceder` (`:15-91`), tudo dentro de **uma
transação** (`beginTransaction`/`commit`/`rollBack` `:19-90`):

| Ordem | Escrita | Alvo | Idempotente? |
|---|---|---|---|
| 1 | `ganharXp` → `UPDATE personagens` (xp, nivel, hp_max, mp_max, [hp_atual, mp_atual se subiu]) | `personagens` | **NÃO** — soma XP toda vez (`ProgressaoService.php:46`) |
| 2 | `UPDATE personagens SET ouro = ouro_antigo + ouro` (`:41`) | `personagens` | **NÃO** — usa `$heroi['ouro']` do **snapshot pré-batalha** (não recarregado) |
| 3 | `ProgressoFase::registrar` upsert (`:44`) | `progresso_fases` | ~sim — `ON DUPLICATE`, mantém `GREATEST(estrelas)`, sobrescreve acertos/erros/usou_ia |
| 4 | Drop de item: `Inventario::adicionar` **só se quantidade==0** (`:48-54`) | `inventario` | ~sim — guarda evita duplicar |
| 5 | `atualizarCapitulo` → possível `UPDATE personagens.capitulo` (`:58`) | `personagens` | sim — só se `novoCapitulo > capitulo` |
| 6 | `avaliarAposFase` → vários `INSERT IGNORE conquistas_personagem` (`:59-61`) | `conquistas_personagem` | sim — `INSERT IGNORE` |
| 7 | `concederConquistaDeRegiao` → `INSERT IGNORE` (`:62`) | `conquistas_personagem` | sim |
| 8 | `concederPuroDeCoracao` → `INSERT IGNORE` (`:63`) | `conquistas_personagem` | sim |
| 9 | Se **não** usou IA: `ReputacaoService::ajustar(+5)` → `UPDATE personagens.reputacao` (`:66-68`) | `personagens` | **NÃO** — soma +5 toda vez |

> ⚠️ **Três escritas não-idempotentes** (1 XP, 2 ouro, 9 reputação) só estão protegidas
> contra dupla-execução pelo estado de **sessão** (`recompensado`/`finalizada`). Ao migrar
> sessão-PHP→sessão-Laravel isso precisa continuar garantido, **ou** mover a guarda para o
> banco (ex.: marcar a batalha como recompensada numa tabela). A ordem 2 lê `ouro` do
> `$heroi` passado ao controller (snapshot antigo) — não recarrega antes de somar; manter
> esse comportamento (ou corrigir com atomicidade) é decisão do port.
>
> Nota: `conceder` respeita transação externa (`$transacaoPropria`, `:20-23`) — se já
> houver transação aberta, não abre outra. No Laravel usar `DB::transaction`.

---

## 5. Superfície de sessão (`$_SESSION`)

Cookie endurecido em `index.php:11-22` (HttpOnly, SameSite=Lax, Secure se HTTPS).
`session_regenerate_id(true)` no login/registro; `session_destroy` no logout.

| Chave | Escrita em | Leitura em | Conteúdo / papel |
|---|---|---|---|
| `usuario_id` | `Auth::login` (`Auth.php:36`), `AuthController::registro` (`:69`) | `Auth::logado/usuario/personagem/exigirLogin` (`Auth.php:58,73,88,106`) | Único dado de identidade em sessão; usuário/personagem são **relidos do banco** e memoizados por request (`Auth.php:64-89`) |
| `csrf` | `csrf_token()` (`helpers.php:476`) | `csrf_valido()` (`helpers.php:491`), `exigirCsrfAjax` (`Controller.php:89`) | Token anti-CSRF (32 bytes hex) |
| `flash` | `Controller::flash` (`Controller.php:57`), `Auth::exigirLogin` (`Auth.php:112`) | Views de layout (mensagem pós-redirect) | `['tipo'=>…, 'mensagem'=>…]` |
| `batalha` | `BatalhaService` (init `:83`; updates `:305,350,375,413,571`; `marcarRecompensado` `:175`); limpo em `limpar` (`:165`) e `fugir` | `BatalhaService::estado/estadoPublico/responder/…` | **Máquina de estado completa da batalha** — incl. `desafios` com o **gabarito** (nunca vai ao cliente; `estadoPublico` remove, `:182-207`). Guarda combo, HP/MP, morte súbita, acumuladores e o flag `recompensado` |

`$_SESSION['batalha']` é o item **mais crítico** do port: é grande, muta a cada turno AJAX,
e contém as respostas corretas (anti-cola). Sessão default do Laravel (cookie/file/db) serve;
avaliar `database`/`redis` para persistir o estado por request AJAX.

---

## 6. Riscos para o port

### 6.1 MySQL → PostgreSQL
1. **`lastInsertId()` sem sequência** (`Model.php:84`) — quebra em pgsql. Trocar por
   `INSERT … RETURNING id` no Model base (afeta todos os `create()`).
2. **`INSERT IGNORE` + `rowCount()`** (`Conquista.php:33-38`) — a detecção de "conquista
   inédita" (`rowCount()>0`) não funciona com `ON CONFLICT DO NOTHING` (0 affected). Usar
   `RETURNING` ou checar existência antes. **Afeta toda a economia de conquistas/objetivos.**
3. **`INSERT … ON DUPLICATE KEY UPDATE` com `VALUES()`/`GREATEST`/`NOW()`**
   (`ProgressoFase.php:36-45`) — reescrever como `ON CONFLICT … DO UPDATE … EXCLUDED.*`.
4. **9 ENUMs** — recriar como tipo PG ou `CHECK`; lembrar dos ENUMs estendidos por migração
   (classe, assunto). Recomendo `CHECK` + `VARCHAR`.
5. **`ON UPDATE CURRENT_TIMESTAMP`** (`personagens.atualizado_em`) — PG não tem; criar
   trigger `BEFORE UPDATE` ou deixar o Eloquent gerenciar `updated_at`.
6. **`YEARWEEK(…,3)`, `NOW() - INTERVAL 7 DAY`, `FIELD()`, `SUM(bool)`** — sem equivalente
   direto; reescrever (missões da semana, recaps, ordenação de inventário, contagem de
   fases perfeitas). Atenção: **semana ISO** deve continuar batendo com
   `MissaoService::indiceSemanaAtual` (que usa `date('o')`/`date('W')` no PHP).
7. **`TINYINT(1)` como boolean** — 8 colunas escrevem `1/0` e comparam `=== 1`/`= 1`.
   Ao virar `boolean` no PG, revisar model/queries (PDO devolve `t`/`f` ou bool) para não
   quebrar comparações `(int)$x === 1`.
8. **`JSON`→`jsonb`** (efeito, opcoes, resposta) — hoje o PHP faz `json_encode/decode`
   manualmente (armazena texto). Em `jsonb` o PG pode reformatar/validar; garantir que o
   decode continue funcionando (`Desafio::decodificar`, `Item::efeito`).
9. **`email UNIQUE` case-insensitive** — collation `utf8mb4_unicode_ci` faz o UNIQUE e o
   `findBy('email')` (`Usuario.php`, `Auth::login`) ignorarem caixa. Em PG (case-sensitive
   por padrão) usar `citext` ou índice `LOWER(email)` + normalização, senão surgem contas
   duplicadas por caixa e logins falham.
10. **Migrador caseiro** (`database/migrate.php`) — parser SQL próprio, idempotência via
    `IF NOT EXISTS`/`INSERT IGNORE`, seeds só em banco vazio. No Laravel migrar para
    `migrations` + `seeders`; os seeds MySQL (`seeds.sql`, `desafios.sql`, etc.) precisam de
    tradução de sintaxe (ENUM, `INSERT IGNORE`, JSON literais).

### 6.2 Sessão-PHP → Sessão-Laravel
1. **Idempotência de recompensa depende da sessão** (`recompensado`/`finalizada`, §4). Se a
   sessão do Laravel expirar/rotacionar entre turnos AJAX, ou se o estado migrar de forma
   diferente, XP/ouro/reputação (escritas não-idempotentes) podem duplicar. Mitigar movendo
   a guarda para o banco.
2. **`$_SESSION['batalha']` gigante e com gabarito** — em cookie-session do Laravel estoura
   4KB. Usar driver `database`/`redis`. Manter a regra anti-cola: `estadoPublico` nunca
   envia respostas ao cliente.
3. **Acesso direto a `$_SESSION`** espalhado (BatalhaService, helpers, Auth, Controller) —
   trocar por `session()`/`Session::` do Laravel; hoje o service escreve `$_SESSION['batalha']`
   diretamente em 6+ pontos.
4. **CSRF muda de modelo** — hoje há duas guardas manuais (`exigirCsrf` POST-only,
   `exigirCsrfAjax` header). O Laravel tem `VerifyCsrfToken` middleware (cobre POST/PUT/PATCH/
   DELETE e header `X-CSRF-TOKEN`). Ao portar, **corrigir de fato** as escritas GET
   (`historia/concluir`, `historia/final`, `auth/logout`) tornando-as POST/DELETE sob o
   middleware — hoje `concluir` grava progresso+XP por GET sem token.
5. **Memoização estática do `Auth`** (`Auth.php:13-24`) — cache por request via propriedades
   `static`. No Laravel isso é resolvido pelo container/guards; remover o cache manual.
6. **`Auth::exigirLogin` trata sessão órfã** (banco recriado → id inválido) fazendo logout
   (`Auth.php:104-116`). Replicar com guard/middleware do Laravel.

---

### Notas IMPLEMENTADO vs. [SÓ DOC]
- Maestria, missões da semana, domínio de região e onboarding **estão implementados** como
  serviços **read-only puros** (config + service + view do perfil), **sem** tabelas/migrations
  próprias — derivam de `respostas_log`/`progresso_fases`/`inventario`. Não há cron/ligas.
- `MEMORY.md` cita "ligas (exige cron)" como **futuro** — **não** existe no código.
- O tom sarcástico dos textos é de sabor; a lógica de correção dos desafios é séria e correta
  (`verificar`, `MestreController::validarDesafio`).
</content>
</invoke>
