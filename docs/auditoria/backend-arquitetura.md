# Auditoria Backend & Arquitetura — Algorithmia

**Data:** 2026-06-18
**Escopo:** leitura completa de `index.php`, `app/core/*`, `app/models/*`, `app/services/*`, `app/controllers/*`, `config/*`, `database/schema.sql`, `database/migrate.php`, `database/migrations/*`
**Status:** READ-ONLY. Nenhum arquivo foi alterado.

---

## 🔴 Crítico

---

### 1. CSRF via GET — write routes acessíveis por link/img

**Arquivo:** `app/core/Controller.php:73-78` / todos os controllers de escrita

**Problema:**
`exigirCsrf()` só verifica o token quando `$_SERVER['REQUEST_METHOD'] === 'POST'`. Em GET, a função retorna silenciosamente sem bloquear. Todas as rotas de escrita abaixo aceitam o item/ID via segmento de URL e são portanto acionáveis via GET:

| Rota GET | Efeito |
|---|---|
| `?url=loja/comprar/5` | gasta ouro e adiciona item |
| `?url=loja/vender/5` | remove item e credita ouro |
| `?url=inventario/equipar/5` | equipa item |
| `?url=inventario/desequipar/5` | desequipa item |
| `?url=inventario/usar/5` | consome poção |
| `?url=inventario/descartar/5` | destrói item permanentemente |
| `?url=mestre/excluirDesafio/5` | apaga desafio do BD (mestre) |
| `?url=mestre/excluirFase/5` | apaga fase + desafios em cascade |
| `?url=mestre/excluirItem/5` | apaga item do catálogo |

Um atacante pode induzir qualquer usuário logado a clicar em link ou carregar `<img src="...url=loja/comprar/99">` para executar essas ações.

**Impacto:** Perda de itens, esvaziamento de ouro, exclusão de conteúdo do jogo (para mestre). Alto.

**Recomendação:** Mudar `exigirCsrf()` para bloquear **qualquer método que não seja POST** (ou rejeitar tokens ausentes independente do método). Converter as rotas de escrita para aceitar apenas POST (`$_SERVER['REQUEST_METHOD'] !== 'POST' → redirecionar`).

**Risco de regressão:** Médio — requer ajuste das âncoras `<a href>` das views de loja/inventário/mestre para `<form method="POST">`.

**Segura?** Sim, desde que as views sejam migradas junto.

---

### 2. Sem transações no fluxo de recompensas de batalha

**Arquivo:** `app/controllers/BatalhaController.php:143-200` (`concederRecompensas`)

**Problema:**
O método executa em sequência, sem transação de banco:
1. `ganharXp` → `UPDATE personagens`
2. `UPDATE personagens` (ouro)
3. `ProgressoFase::registrar` → `INSERT … ON DUPLICATE KEY UPDATE`
4. `Inventario::adicionar` (drop) → SELECT + UPDATE/INSERT
5. `atualizarCapitulo` → `UPDATE personagens`
6. `avaliarAposFase` → múltiplos INSERTs em `conquistas_personagem`
7. `ReputacaoService::ajustar` → SELECT + UPDATE

Se a conexão cair, o processo PHP morrer (OOM kill, timeout) ou qualquer statement intermediário falhar depois do passo 1, o personagem pode ter recebido XP/ouro sem ter o progresso registrado, ou ter as conquistas mas não o capítulo avançado.

**Impacto:** Estado corrompido de personagem; difícil de detectar e corrigir manualmente. Alto.

**Recomendação:** Envolver todo `concederRecompensas` em `$pdo->beginTransaction()` / `commit()` / `rollBack()`. Expor `getConnection()` ao controller (já disponível via `getConnection()`).

**Risco de regressão:** Médio — requer passar a PDO connection explicitamente ou via um método do Model base.

**Segura?** Sim com teste de rollback.

---

### 3. schema.sql dessincronizado da migration `assunto calculo`

**Arquivo:** `database/schema.sql:113` vs `database/migrations/20250618-assunto-calculo.sql`

**Problema:**
`schema.sql` define o ENUM `assunto` sem `'calculo'`. A migration `20250618-assunto-calculo.sql` adiciona esse valor via `ALTER TABLE`. O fluxo correto é: schema → migration → seeds, e isso funciona em `migrate.php`.

Porém, `--schema` (linha de comando) aplica apenas o schema e depois as migrations — então `--schema` também fica OK. O problema real é que qualquer ferramenta de comparação de schema (diff, staging deploy manual) verá a coluna `assunto` sem `'calculo'` no schema canônico, causando confusão ou regressão se alguém criar o banco a partir do schema.sql isolado sem rodar migrations.

**Impacto:** Confusão operacional; queries que inserem `assunto='calculo'` falhariam em bancos criados sem migrations.

**Recomendação:** Atualizar `schema.sql` linha 113 para incluir `'calculo'` no ENUM. Manter a migration por compatibilidade com bancos existentes.

**Risco de regressão:** Baixo — mudança puramente no schema, sem lógica PHP.

**Segura?** Sim.

---

## 🟠 Alto

---

### 4. TOCTOU no saldo de ouro (loja e recompensa)

**Arquivo:** `app/controllers/LojaController.php:34,39` e `BatalhaController.php:161`

**Problema:**
O padrão é: (1) ler o ouro do `$heroi` carregado no início da requisição; (2) calcular novo valor; (3) salvar. Se o mesmo usuário abrir duas abas e acionar duas compras simultaneamente, ambas leem o mesmo saldo e ambas subtraem do mesmo valor inicial — o ouro é debitado duas vezes mas apenas uma das transações reflete o estado real.

```php
// LojaController::comprar — lê $heroi['ouro'] da sessão/início da req
(new Personagem())->update($id, ['ouro' => (int) $heroi['ouro'] - $item['preco']]);
```

**Impacto:** Exploração intencional pode duplicar compras. Moderado (educacional, baixo incentivo de fraude), mas correto de resolver.

**Recomendação:** Usar `UPDATE personagens SET ouro = ouro - :preco WHERE id = :id AND ouro >= :preco` e verificar `rowCount() === 1`. Padrão atômico sem SELECT prévio.

**Risco de regressão:** Baixo — isolado em LojaController e BatalhaController.

**Segura?** Sim.

---

### 5. `Escolha::definir` — DELETE + INSERT sem transação e sem UNIQUE constraint

**Arquivo:** `app/models/Escolha.php:12-17` + `database/schema.sql:196-203`

**Problema:**
A tabela `escolhas` não tem UNIQUE em `(personagem_id, codigo)`. O método `definir()` faz DELETE seguido de INSERT como dois statements separados. Entre eles, outro processo pode inserir uma linha duplicada, resultando em duas escolhas para o mesmo código. Além disso, sem a constraint UNIQUE o `Escolha::valor()` pode retornar a linha errada se houver duplicata.

**Impacto:** Corrupção do final narrativo; a consulta `valor()` usa `LIMIT 1` sem `ORDER BY`, retornando valor não-determinístico.

**Recomendação:** (a) Adicionar `UNIQUE KEY uq_escolha (personagem_id, codigo)` no schema; (b) Substituir DELETE+INSERT por `INSERT … ON DUPLICATE KEY UPDATE valor = VALUES(valor)`.

**Risco de regressão:** Baixo — a migration adiciona a constraint; não afeta dados existentes se não há duplicatas.

**Segura?** Sim.

---

### 6. `Inventario::adicionar` e `remover` — race condition (SELECT → UPDATE/INSERT)

**Arquivo:** `app/models/Inventario.php:40-65`

**Problema:**
Ambos os métodos fazem `pegar()` (SELECT) seguido de `update()` ou `create()`. Duas requisições concorrentes para o mesmo personagem+item podem ler o mesmo estado e aplicar incrementos duplicados ou decrementar abaixo de zero.

O schema tem `UNIQUE KEY uq_inv (personagem_id, item_id)`, o que pode causar erro 1062 se dois `create()` rodarem simultaneamente.

**Impacto:** Quantidade inconsistente de itens; possível erro fatal não tratado.

**Recomendação:** Usar `INSERT … ON DUPLICATE KEY UPDATE quantidade = quantidade + :qtd` para `adicionar`. Para `remover`, usar `UPDATE … SET quantidade = quantidade - :qtd WHERE quantidade >= :qtd` e verificar `rowCount`.

**Risco de regressão:** Médio — requer testes de inventário.

**Segura?** Sim se testado.

---

### 7. Ausência de rate limiting no endpoint de login

**Arquivo:** `app/controllers/AuthController.php:10-27`

**Problema:**
`Auth::login()` não possui nenhum mecanismo de limitação de tentativas. Um atacante pode realizar ataques de força bruta ilimitados contra qualquer email via `?url=auth/login` (POST).

**Impacto:** Comprometimento de contas; especialmente crítico para a conta mestre.

**Recomendação:** Implementar bloqueio por IP ou por email após N tentativas falhas (usando sessão ou tabela de tentativas). Alternativa mínima: sleep exponencial após falhas.

**Risco de regressão:** Baixo — adição pura.

**Segura?** Sim.

---

## 🟡 Médio

---

### 8. Dead code: `Desafio::daFase()` nunca usada

**Arquivo:** `app/models/Desafio.php:9-13`

**Problema:**
`daFase()` e `poolDaFase()` são idênticas (mesma query, mesmo retorno). Apenas `poolDaFase()` é chamada em `BatalhaService`. `daFase()` não é referenciada em nenhum controller, service ou view.

**Impacto:** Código morto que confunde mantenedores sobre qual método usar.

**Recomendação:** Remover `daFase()` ou fazer `daFase()` ser um alias explícito de `poolDaFase()` com `@deprecated`.

**Risco de regressão:** Baixo — confirmar via `grep -rn '->daFase'` (zero ocorrências).

**Segura?** Sim.

---

### 9. `ProgressoFase::registrar` usa `VALUES()` depreciado no MySQL 8.0.20+

**Arquivo:** `app/models/ProgressoFase.php:37-48`

**Problema:**
```sql
ON DUPLICATE KEY UPDATE
    estrelas = GREATEST(estrelas, VALUES(estrelas)),
    acertos  = VALUES(acertos), ...
```
A função `VALUES()` em cláusulas `ON DUPLICATE KEY UPDATE` foi depreciada no MySQL 8.0.20 e emite warning. Em versões futuras do MySQL será removida.

**Impacto:** Warnings de depreciação nos logs; risco de quebra em upgrade de MySQL.

**Recomendação:** Migrar para aliases de linha:
```sql
INSERT INTO progresso_fases (...) VALUES (...) AS novo
ON DUPLICATE KEY UPDATE
    estrelas = GREATEST(estrelas, novo.estrelas),
    acertos  = novo.acertos, ...
```

**Risco de regressão:** Baixo — mudança de sintaxe SQL equivalente.

**Segura?** Sim.

---

### 10. `BatalhaService::atributosCombate` chama `getConnection()` diretamente, bypassando o Model

**Arquivo:** `app/services/BatalhaService.php:352-363` e método `db():PDO` (linha 366)

**Problema:**
O método faz uma query SQL manual via `$this->db()` (que chama `getConnection()`) em vez de usar um Model. Isso quebra o padrão arquitetural: a lógica de acesso a dados de inventário equipado está duplicada fora de `Inventario`.

**Impacto:** Manutenção fragmentada; se a tabela `inventario` mudar de estrutura, esse ponto não será encontrado pelo `grep` em models.

**Recomendação:** Adicionar método `doPersonagemEquipado(int $personagemId): array` em `Inventario` e chamá-lo de `atributosCombate`.

**Risco de regressão:** Baixo — refatoração de extração de método.

**Segura?** Sim.

---

### 11. IDs de fase hardcoded em `HistoriaController`

**Arquivo:** `app/controllers/HistoriaController.php:65,79`

**Problema:**
```php
if ((int) $fase['ordem_global'] === 34) { $this->redirect('historia/ver/35'); }
if (!(new ProgressoFase())->concluiu((int) $heroi['id'], 35)) { ... }
```
Os IDs de banco `34` e `35` e a `ordem_global` `34` estão hardcoded. Se o Mestre reordenar fases no painel admin, a lógica do final quebra silenciosamente.

**Impacto:** Jogo termina antes ou não termina ao reordenar fases.

**Recomendação:** Usar `tipo = 'chefe_final'` como critério em vez de `id`/`ordem_global` hardcoded. Buscar a fase do tipo `chefe_final` dinamicamente.

**Risco de regressão:** Médio — requer query adicional mas elimina fragilidade.

**Segura?** Sim.

---

### 12. `ConquistaService::avaliarAposFase` — IDs de fases secundárias hardcoded

**Arquivo:** `app/services/ConquistaService.php:61`

**Problema:**
```php
$secundarias = [8, 14, 20, 32];
```
Os IDs das fases secundárias estão hardcoded. Se uma fase secundária for excluída ou criada no painel, a conquista `arquivista_do_vazio` nunca será concedida (ou será concedida com critério errado).

**Recomendação:** Buscar fases do tipo `'secundaria'` dinamicamente via `Fase::where('tipo', 'secundaria')`.

**Risco de regressão:** Baixo — query simples.

**Segura?** Sim.

---

### 13. `Model::update` falha silenciosamente com `$data` vazio

**Arquivo:** `app/core/Model.php:87-96`

**Problema:**
Se `$data` for um array vazio, `$sets` fica vazio e a query gerada é `UPDATE table SET  WHERE id = :id` — SQL inválida que gera `PDOException`. Não há guard para esse caso.

**Impacto:** Erro 500 não tratado em qualquer chamada acidental com array vazio.

**Recomendação:** Adicionar no início de `update()`:
```php
if (empty($data)) { return true; }
```

**Risco de regressão:** Nenhum — adição defensiva.

**Segura?** Sim.

---

### 14. Ausência de `Content-Security-Policy` nos headers de segurança

**Arquivo:** `index.php:25-28`

**Problema:**
O front controller envia `X-Content-Type-Options`, `X-Frame-Options` e `Referrer-Policy`, mas não envia `Content-Security-Policy`. Sem CSP, scripts inline maliciosos (caso haja XSS) não são bloqueados pelo navegador.

**Recomendação:** Adicionar CSP restritiva:
```php
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data:;");
```
Ajustar `unsafe-inline` para estilos apenas se necessário (verificar se há `style` inline nas views).

**Risco de regressão:** Médio — pode bloquear assets externos ou inline. Testar em staging.

**Segura?** Sim com testes.

---

### 15. `Auth::exigirMestre()` usa `die()` com HTML inline (403)

**Arquivo:** `app/core/Auth.php:131`

**Problema:**
```php
die('<p style="font-family:monospace;padding:2rem;">403 — Apenas Mestres...</p>');
```
Bypassa o sistema de views e o layout. Inconsistente com o padrão de 404 (`/app/views/errors/404.php`).

**Recomendação:** Criar `app/views/errors/403.php` e redirecionar para lá, seguindo o padrão do Router.

**Risco de regressão:** Nenhum — melhoria estética/arquitetural.

**Segura?** Sim.

---

### 16. `getConnection($semBanco=true)` não atualiza o singleton

**Arquivo:** `config/db.php:37-68`

**Problema:**
Quando chamado com `$semBanco=true`, a função cria um novo PDO mas não o armazena em `$conexao`. A variável `static $conexao` permanece `null` até a primeira chamada com `$semBanco=false`. Isso significa que se `getConnection(true)` for chamado dentro do fluxo web normal (não é atualmente), gera um segundo PDO sem banco, desperdiçando conexão. O padrão atual funciona apenas porque `migrate.php` é o único caller de `getConnection(true)`.

**Impacto:** Baixo hoje; risco latente se o padrão for reutilizado.

**Recomendação:** Documentar que `$semBanco=true` é uso exclusivo do migrador. Ou usar variável separada para conexão sem banco.

**Risco de regressão:** Nenhum.

**Segura?** Sim.

---

## 🟢 Melhorias

---

### 17. `Dialogo::paraMomento` — fallback recursivo (profundidade 2, aceitável mas frágil)

**Arquivo:** `app/models/Dialogo.php:22-24`

**Problema:**
O fallback para `'padrao'` é implementado via chamada recursiva. Se alguém chamar `paraMomento(..., 'padrao')` e não houver resultado, a função chama a si mesma com `'padrao'` novamente (a condição `$variante !== 'padrao'` impede loop infinito). Correto funcionalmente, mas o padrão recursivo dificulta leitura.

**Recomendação:** Substituir por uma query com `WHERE variante IN (:v, 'padrao') ORDER BY (variante = :v) DESC LIMIT ...` ou simplesmente dois SELECTs inline.

**Risco de regressão:** Baixo.

**Segura?** Sim.

---

### 18. `ReputacaoService::ajustar` faz SELECT + UPDATE sem atomicidade

**Arquivo:** `app/services/ReputacaoService.php:20-25`

**Problema:**
```php
$p = $this->personagens->findById($personagemId);
$novo = max(REPUTACAO_MIN, min(REPUTACAO_MAX, (int) $p['reputacao'] + $delta));
$this->personagens->update($personagemId, ['reputacao' => $novo]);
```
Mesmo padrão TOCTOU do ouro: duas chamadas simultâneas podem sobrescrever o ajuste uma da outra.

**Recomendação:** `UPDATE personagens SET reputacao = GREATEST(-100, LEAST(100, reputacao + :delta)) WHERE id = :id`.

**Risco de regressão:** Baixo.

**Segura?** Sim.

---

### 19. Falta de índices em `respostas_log` para queries analíticas

**Arquivo:** `database/schema.sql:208-217`

**Problema:**
`respostas_log` só tem foreign keys (que criam índices automáticos em InnoDB). As queries `estatisticasPorAssunto`, `totalRespostas`, `totalUsosIa` filtram por `personagem_id` — coberto pelo FK. Mas a query em `Desafio::idsVistos` faz JOIN `respostas_log r JOIN desafios d ON d.id = r.desafio_id WHERE r.personagem_id = :p AND d.fase_id = :f`. O plano de execução pode ser ineficiente sem índice composto em `(personagem_id, desafio_id)`.

**Recomendação:** Adicionar `INDEX idx_log_p_d (personagem_id, desafio_id)` via migration.

**Risco de regressão:** Nenhum — índice adicionado.

**Segura?** Sim.

---

### 20. `MapaController` instancia `Mestre` duas vezes na mesma requisição

**Arquivo:** `app/controllers/MapaController.php:12,46`

**Problema:**
`Fase::comMestre()` já faz JOIN com mestres e retorna `mestre_nome` e `cor_tema`. Logo abaixo, `(new Mestre())->todosOrdenados()` faz um segundo SELECT completo para pegar `svg_slug` e `regiao`. As informações poderiam ser carregadas em uma única query.

**Impacto:** Uma query extra por carregamento do mapa.

**Recomendação:** Incluir `m.svg_slug`, `m.regiao` na query de `Fase::comMestre()`.

**Risco de regressão:** Baixo.

**Segura?** Sim.

---

### 21. `BatalhaController::concederConquistaDeRegiao` instancia `ConquistaService` separado

**Arquivo:** `app/controllers/BatalhaController.php:211,219`

**Problema:**
`concederRecompensas` já instancia `(new ConquistaService())` na linha 179. Os métodos privados `concederConquistaDeRegiao` e `concederPuroDeCoracao` instanciam novos `ConquistaService` e `ProgressoFase` desnecessariamente — cada instância abre uma query desnecessária para criar o Model.

**Recomendação:** Passar os services já instanciados como parâmetros ou extrair para propriedade da classe.

**Risco de regressão:** Nenhum.

**Segura?** Sim.

---

## Tabela-Resumo

| # | Achado | Severidade | Arquivo Principal | Esforço | Seguro? |
|---|---|---|---|---|---|
| 1 | CSRF via GET — rotas de escrita acessíveis por link | 🔴 Crítico | `Controller.php:73` | Médio | Sim (com ajuste de views) |
| 2 | Sem transação no fluxo de recompensas | 🔴 Crítico | `BatalhaController.php:143` | Médio | Sim |
| 3 | schema.sql sem `'calculo'` no ENUM assunto | 🔴 Crítico | `schema.sql:113` | Baixo | Sim |
| 4 | TOCTOU no saldo de ouro | 🟠 Alto | `LojaController.php:39` | Baixo | Sim |
| 5 | `Escolha::definir` DELETE+INSERT não-atômico + sem UNIQUE | 🟠 Alto | `Escolha.php:12` | Baixo | Sim |
| 6 | Race condition em `Inventario::adicionar/remover` | 🟠 Alto | `Inventario.php:40` | Baixo | Sim |
| 7 | Sem rate limiting no login | 🟠 Alto | `AuthController.php:10` | Médio | Sim |
| 8 | Dead code: `Desafio::daFase()` | 🟡 Médio | `Desafio.php:9` | Baixo | Sim |
| 9 | `VALUES()` depreciado no MySQL 8.0.20+ | 🟡 Médio | `ProgressoFase.php:40` | Baixo | Sim |
| 10 | `atributosCombate` acessa BD fora do Model | 🟡 Médio | `BatalhaService.php:352` | Baixo | Sim |
| 11 | IDs de fase hardcoded em `HistoriaController` | 🟡 Médio | `HistoriaController.php:65,79` | Médio | Sim |
| 12 | IDs de fases secundárias hardcoded em `ConquistaService` | 🟡 Médio | `ConquistaService.php:61` | Baixo | Sim |
| 13 | `Model::update` falha silenciosamente com `$data` vazio | 🟡 Médio | `Model.php:87` | Baixo | Sim |
| 14 | Ausência de `Content-Security-Policy` | 🟡 Médio | `index.php:25` | Baixo | Sim (testar) |
| 15 | `exigirMestre` usa `die()` com HTML inline (sem view 403) | 🟡 Médio | `Auth.php:131` | Baixo | Sim |
| 16 | `getConnection(semBanco=true)` não armazena no singleton | 🟡 Médio | `db.php:37` | Baixo | Sim |
| 17 | `Dialogo::paraMomento` fallback recursivo | 🟢 Melhoria | `Dialogo.php:22` | Baixo | Sim |
| 18 | `ReputacaoService::ajustar` TOCTOU | 🟢 Melhoria | `ReputacaoService.php:20` | Baixo | Sim |
| 19 | Falta índice composto em `respostas_log` | 🟢 Melhoria | `schema.sql:208` | Baixo | Sim |
| 20 | `MapaController` instancia `Mestre` duas vezes | 🟢 Melhoria | `MapaController.php:12,46` | Baixo | Sim |
| 21 | `ConquistaService` instanciado duas vezes em recompensas | 🟢 Melhoria | `BatalhaController.php:179,211` | Baixo | Sim |

---

## Pontos Positivos (para referência)

- **Router robusto:** usa `ReflectionMethod` para bloquear métodos herdados e protegidos. Não expõe métodos internos de `Controller`.
- **Anti-cola correto:** gabarito nunca enviado ao cliente; `estadoPublico()` filtra a resposta antes de sair do servidor.
- **Auth.php com memoização:** evita SELECTs repetidos por requisição sem stale state (invalidação correta em login/logout).
- **Model base seguro:** `colunaSegura()` e `ordenacaoSegura()` previnem SQL injection em nomes de coluna, onde PDO não pode parametrizar.
- **Session hardening:** cookie com `HttpOnly`, `SameSite=Lax` e `Secure` condicional. `session_regenerate_id(true)` no login.
- **Migrador idempotente:** schema `CREATE IF NOT EXISTS` + tabela de controle de migrations aplicadas.
- **Anti-repetição de desafios:** priorização de inéditos via `idsVistos()` com ordem crescente de dificuldade — design pedagógico correto.
