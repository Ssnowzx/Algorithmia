# Auditoria de Segurança — Algorithmia
**Data:** 2026-06-18 | **Auditor:** Claude Code (read-only, defensivo) | **Branch:** refactor/auditoria-qualidade-producao

---

## 🔴 Crítico

### 1. Logout incompleto — sessão PHP não destruída
**Arquivo:** `app/core/Auth.php:41-45`

**Vetor:** `Auth::logout()` faz apenas `unset($_SESSION['usuario_id'], $_SESSION['batalha'])`. A sessão PHP continua viva no servidor com o mesmo `session_id`. Se um atacante obteve o cookie de sessão (via XSS residual, rede não-HTTPS, log de acesso) ele continua autenticado após o logout da vítima.

**Impacto:** Session hijacking pós-logout; bypass completo de autenticação.

**Correção:**
```php
public static function logout(): void
{
    self::limparMemo();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}
```
**Risco de regressão:** Baixo — nenhuma lógica depende do `session_id` antigo após logout.

---

### 2. Race condition em ouro — compra duplicada simultânea
**Arquivo:** `app/controllers/LojaController.php:34-40`

**Vetor:** O saldo de ouro é lido via `Auth::personagem()` (memoizado na requisição), e a atualização é `UPDATE personagens SET ouro = <lido - preco> WHERE id = ...`. Não há `SELECT FOR UPDATE` nem transação atômica. Duas requisições simultâneas de compra (`batalha/comprar`) veem o mesmo saldo e ambas passam na verificação `heroi['ouro'] >= preco`, gerando saldo negativo.

**Impacto:** Compra de itens sem ter ouro suficiente; corrupção de saldo.

**Correção:**
```sql
-- No LojaController::comprar(), substituir a leitura simples por:
BEGIN;
SELECT ouro FROM personagens WHERE id = :id FOR UPDATE;
-- verificar saldo, depois UPDATE
COMMIT;
```
Ou usar `UPDATE personagens SET ouro = ouro - :preco WHERE id = :id AND ouro >= :preco` e checar `rowCount() === 1`.

**Risco de regressão:** Médio — requer gerenciar transação PDO manualmente.

---

## 🟠 Alto

### 3. Senha mínima de 6 caracteres — política fraca
**Arquivo:** `app/controllers/AuthController.php:54-55`

**Vetor:** Senha de 6 chars (`mb_strlen($senha) < 6`) é trivialmente quebrável por força-bruta, especialmente sem limitador de tentativas (nenhum rate-limit está implementado). A conta demo usa `qwe123` — 6 chars.

**Impacto:** Contas de jogadores comprometidas por força-bruta; painel Mestre exposto se senha do admin for fraca.

**Correção:** Elevar para mínimo 8 caracteres. Implementar bloqueio temporário (ex.: `$_SESSION['login_attempts']` + `sleep()` progressivo, ou tabela `login_attempts` com timestamp).

**Risco de regressão:** Baixo — apenas altera a validação de registro; não afeta contas existentes.

---

### 4. Sem Content-Security-Policy — XSS amplificado
**Arquivo:** `index.php:25-27`

**Vetor:** Headers enviados são `X-Content-Type-Options`, `X-Frame-Options` e `Referrer-Policy`. Falta `Content-Security-Policy`. Qualquer XSS residual (campos de banco exibidos por mestre sem escape, etc.) pode executar JavaScript arbitrário sem restrição.

**Impacto:** Se uma cadeia XSS for descoberta (banco comprometido com payload em `pergunta`/`nome`), o atacante tem execução irrestrita no contexto do usuário.

**Correção:**
```php
// index.php, após os headers existentes:
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'nonce-{$nonce}'; style-src 'self' https://fonts.googleapis.com 'unsafe-inline'; font-src https://fonts.gstatic.com; img-src 'self' data:; frame-ancestors 'none'");
```
(Gerar `$nonce` aleatório por requisição e passá-lo para as views via `extract()`.)

**Risco de regressão:** Alto — o projeto usa `<script>` inline extensivamente em views (arena.php, header.php); exige adicionar `nonce` em todas as tags `<script>` ou migrar para arquivos externos.

---

### 5. IP de produção hardcoded em arquivo rastreado pelo projeto
**Arquivo:** `database/seed_remote.sh:14`

```bash
HOST="${1:-129.121.33.89}"
```

**Vetor:** O endereço IP da VPS de produção está como default no script, que provavelmente é (ou foi) rastreado pelo git. Isso revela a infraestrutura ao público.

**Impacto:** Atacantes conseguem o alvo sem precisar de reconhecimento ativo; simplifica ataques direcionados ao MySQL (porta 3306) e ao servidor HTTP.

**Correção:** Remover o default hardcoded; tornar o argumento obrigatório ou usar variável de ambiente. Adicionar `database/seed_remote.sh` ao `.gitignore`.

**Risco de regressão:** Nenhum — o script continua funcionando passando o host como argumento.

---

### 6. CSRF nos endpoints AJAX de batalha — ausente
**Arquivos:** `app/controllers/BatalhaController.php:64-115`; `public/js/batalha.js:19-25`

**Vetor:** Os métodos `responder()`, `fragmento()`, `especial()`, `pocao()` e `fugir()` **não chamam** `$this->exigirCsrf()`. A função `csrf_valido()` lê `$_POST['csrf']`, mas esses endpoints recebem `Content-Type: application/json` via `fetch()`, cujo corpo é lido por `corpoJson()` (não `$_POST`).

SameSite=Lax mitiga ataques cross-site iniciados por navegação de terceiros, mas **não protege** contra ataques de formulário dentro do mesmo site (XSS) nem contra ambientes onde o SameSite não é aplicado (browsers antigos, sub-domínios).

**Impacto:** Um XSS residual pode forçar turnos de batalha, gastar Fragmentos da IA, despejar poções ou encerrar batalhas em andamento de outro usuário (se o `session_id` for compartilhado).

**Correção:**
- Incluir o CSRF no corpo JSON: `{ "csrf": "...", "resposta": ... }` e validar em `corpoJson()` + `exigirCsrf()` adaptado.
- Alternativamente, enviar o token como header customizado (`X-CSRF-Token`) e validá-lo no servidor.

**Risco de regressão:** Médio — requer alterar `batalha.js` (adicionar o token nos corpos) e a validação PHP.

---

### 7. Enum fields sem whitelist — MestreController
**Arquivo:** `app/controllers/MestreController.php:92, 122-123, 176, 244, 248`

**Vetor:** Os campos `tipo`, `assunto`, `raridade` vêm direto de `$_POST` sem validação contra lista permitida:
```php
'tipo'    => $_POST['tipo'] ?? 'multipla',    // não validado
'assunto' => $_POST['assunto'] ?? 'logica',   // não validado
'raridade' => $_POST['raridade'] ?? 'comum',  // não validado
```
O `Model::create/update` usa `colunaSegura()` nos nomes de colunas, mas não nos **valores**. Se o banco usa ENUM e a inserção de valor inválido for permitida (modo SQL permissivo), dados inconsistentes entram. Se o valor for refletido sem escape em views, vira XSS armazenado.

**Impacto:** Dados corrompidos no banco; potencial XSS armazenado exibido para todos os jogadores se o campo for renderizado sem `e()`.

**Correção:** Validar contra arrays de valores permitidos antes de inserir:
```php
$tiposPermitidos = ['multipla', 'vf', 'completar', 'erro', 'ordenar', 'arrastar'];
if (!in_array($_POST['tipo'] ?? '', $tiposPermitidos, true)) { /* erro */ }
```

**Risco de regressão:** Baixo — acesso restrito a mestre; validação só rejeita inputs inválidos.

---

## 🟡 Médio

### 8. Logout via GET — CSRF trivial
**Arquivo:** `app/controllers/AuthController.php:77-80`; `app/views/layout/header.php:91`

**Vetor:** O logout é disparado por `<a href="auth/logout">` (GET), sem token CSRF. Qualquer página de terceiro pode embedar `<img src="https://app.example.com/index.php?url=auth/logout">` e deslogar silenciosamente o usuário.

**Impacto:** Logout forçado (denial of service de sessão); em contextos de prova/competição isso é disruptivo.

**Correção:** Converter logout para POST com token CSRF:
```html
<form method="post" action="<?= url('auth/logout') ?>" style="display:inline">
    <?= csrf_field() ?>
    <button type="submit" class="link-sair">Sair</button>
</form>
```

**Risco de regressão:** Baixo — apenas muda o verbo HTTP; requer adicionar `$this->exigirCsrf()` em `logout()`.

---

### 9. Sessão de batalha não vinculada ao `personagem_id`
**Arquivo:** `app/services/BatalhaService.php:114-116, 181-184`

**Vetor:** `BatalhaService::estado()` lê `$_SESSION['batalha']` sem verificar se `$estado['personagem_id']` corresponde ao `heroi['id']` da sessão atual. Em cenário de compartilhamento de conta (dois logins simultâneos com o mesmo `session_id`), um usuário poderia processar a batalha do outro.

**Impacto:** Baixo em prática (requer mesmo session_id), mas o gap é defensivo.

**Correção:** Em `responder()`, `usarFragmentoIa()` e `usarPocao()`, verificar:
```php
if ((int) $estado['personagem_id'] !== (int) $heroi['id']) {
    $this->limpar();
    return ['erro' => 'Estado de batalha inválido.'];
}
```

**Risco de regressão:** Nenhum.

---

### 10. Senha padrão da conta demo documentada publicamente
**Arquivo:** `database/seed-conta-demo.php:25-26`; `docs/` e `MEMORY.md`

**Vetor:** As credenciais `masterboss@boss.com / qwe123` estão codificadas como default e mencionadas em documentação (`pipeline-migrations.md`). Mesmo que substituíveis por `DEMO_EMAIL`/`DEMO_SENHA`, se o operador rodar sem definir as variáveis, a conta de **mestre** entra no ar com senha pública.

**Impacto:** Acesso completo ao painel de administração (CRUD de fases, desafios, itens).

**Correção:** Exigir variáveis de ambiente obrigatórias (`exit(1)` se ausentes) em produção, ou gerar senha aleatória no primeiro run e exibi-la apenas uma vez.

**Risco de regressão:** Médio — operadores de dev dependem dos defaults; exige documentar o fluxo.

---

### 11. `SameSite=Lax` em vez de `Strict`
**Arquivo:** `index.php:20`

**Vetor:** `SameSite=Lax` ainda permite que o cookie de sessão seja enviado em navegações de topo (links GET cross-site). Para um RPG escolar, `Strict` seria mais seguro sem sacrificar UX (bookmarks e links diretos ainda funcionam após uma visita prévia ao site).

**Impacto:** Baixo isolado, mas aumenta superfície de ataque CSRF combinado com item #8.

**Correção:** Mudar `'samesite' => 'Strict'` em `index.php:20`.

**Risco de regressão:** Links externos para o site vão exigir que o usuário esteja já logado ou faça login novamente — aceitável.

---

### 12. Ausência de HSTS
**Arquivo:** `index.php:25-27`

**Vetor:** Não há `Strict-Transport-Security`. Em deploy HTTPS, um downgrade attack inicial pode capturar cookies (mesmo `httponly` não protege contra MITM no primeiro request).

**Correção:**
```php
if ($ehHttps) {
    header('Strict-Transport-Security: max-age=63072000; includeSubDomains');
}
```

**Risco de regressão:** Nenhum.

---

## 🟢 Seguro — Achados Positivos

| Item | Arquivo | Status |
|------|---------|--------|
| Prepared statements em todo Model base | `app/core/Model.php` | ✅ SEGURO |
| Validação de coluna (`colunaSegura`) antes de interpolação | `app/core/Model.php:22-27` | ✅ SEGURO |
| `password_hash(PASSWORD_DEFAULT)` + `password_verify()` | `AuthController:65`, `Auth:32` | ✅ SEGURO |
| `session_regenerate_id(true)` no login e registro | `Auth:35`, `AuthController:68` | ✅ SEGURO |
| Cookie `HttpOnly: true` + `Secure` condicional | `index.php:15-21` | ✅ SEGURO |
| CSRF com `hash_equals` em todos os forms POST | `helpers.php:397-401` | ✅ SEGURO |
| Saída escapada via `e()` = `htmlspecialchars(ENT_QUOTES, UTF-8)` | `helpers.php:9-12` | ✅ SEGURO |
| Gabarito nunca enviado ao cliente (`estadoPublico()`) | `BatalhaService:127-150` | ✅ SEGURO |
| Verificação de ownership de inventário via `pegar(heroi_id, itemId)` | `Inventario:28-35` | ✅ SEGURO |
| Scripts de banco protegidos por `PHP_SAPI !== 'cli'` | `migrate.php:16-19`, `seed-conta-demo.php:14-17` | ✅ SEGURO |
| `.htaccess` bloqueia acesso web a `database/`, `config/`, `app/` | `.htaccess:15` | ✅ SEGURO |
| Roteador valida métodos públicos via `ReflectionMethod` | `Router:45-53` | ✅ SEGURO |
| `Auth::exigirMestre()` no `__construct` de `MestreController` | `MestreController:10` | ✅ SEGURO |
| Verificação de fase liberada antes de batalha/história | `BatalhaController:26-29`, `HistoriaController:21-25` | ✅ SEGURO |
| `.gitignore` exclui `.env` e `config/db.local.php` | `.gitignore` | ✅ SEGURO |
| `PDO::ATTR_EMULATE_PREPARES => false` | `config/db.php:46` | ✅ SEGURO |
| Erro de banco não vaza detalhes em produção | `config/db.php:55-65` | ✅ SEGURO |
| XSS impossível por SQL injection (PDO real prepares) | `config/db.php:46` + `Model.php` | ✅ SEGURO |

---

## Tabela-Resumo

| # | Título | Severidade | Arquivo Principal | Vetor |
|---|--------|-----------|-------------------|-------|
| 1 | Logout incompleto (sessão não destruída) | 🔴 Crítico | `app/core/Auth.php:41` | Session hijacking |
| 2 | Race condition no ouro (compra dupla) | 🔴 Crítico | `LojaController.php:34-40` | Saldo negativo |
| 3 | Senha mínima 6 chars, sem rate-limit | 🟠 Alto | `AuthController.php:54` | Força-bruta |
| 4 | Sem Content-Security-Policy | 🟠 Alto | `index.php:25-27` | XSS amplificado |
| 5 | IP de produção hardcoded em script rastreado | 🟠 Alto | `database/seed_remote.sh:14` | Info disclosure |
| 6 | CSRF ausente nos endpoints AJAX de batalha | 🟠 Alto | `BatalhaController.php:64-115` | CSRF via XSS |
| 7 | Enum fields sem whitelist no MestreController | 🟠 Alto | `MestreController.php:92,123` | XSS armazenado |
| 8 | Logout via GET sem CSRF | 🟡 Médio | `AuthController.php:77` | Logout forçado |
| 9 | Estado de batalha não vinculado ao personagem | 🟡 Médio | `BatalhaService.php:181` | Session confusion |
| 10 | Senha demo pública como default | 🟡 Médio | `seed-conta-demo.php:26` | Admin takeover |
| 11 | SameSite=Lax em vez de Strict | 🟡 Médio | `index.php:20` | Superfície CSRF |
| 12 | Ausência de HSTS | 🟡 Médio | `index.php:25-27` | Downgrade attack |

---

## Prioridade de Correção

1. **Imediato (produção):** #1 (logout), #2 (race condition ouro), #5 (IP exposto), #10 (senha demo)
2. **Próximo sprint:** #3 (política de senha + rate-limit), #6 (CSRF AJAX), #7 (enum whitelist), #8 (logout via GET)
3. **Hardening:** #4 (CSP), #9 (batalha ownership), #11 (SameSite), #12 (HSTS)
