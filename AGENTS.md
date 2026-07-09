# AGENTS.md — Guia para agentes de IA (Codex e afins)

Instruções obrigatórias para qualquer agente de codificação que trabalhe neste
repositório. Complementa o [`CLAUDE.md`](CLAUDE.md) (convenções de código, git,
testes e segurança) e os docs em [`docs/desenvolvimento/`](docs/desenvolvimento/).
**Leia antes de agir. Em dúvida sobre algo destrutivo ou que afete dados de
jogadores: PARE e pergunte ao time.**

O projeto é o **Algorithmia** — jogo educativo de RPG.

---

## ⚠️ Este repositório tem DUAS bases de código

Antes de tocar em qualquer arquivo, saiba em qual você está.

| | **Legado** (raiz) | **Port** (`platform/`) |
|---|---|---|
| Stack | PHP puro, MVC artesanal, PDO/MySQL | Laravel 13, PostgreSQL 18 |
| Status | **em produção** | completo, **corte não executado** |
| Papel | plano de rollback do corte | onde o trabalho novo acontece |
| Testes | 38 vetores-ouro (`tests/`) | 196 testes (`platform/tests/`) |

**Trabalho novo vai para o `platform/`.** O legado só recebe correção urgente — ele
será aposentado no corte.

> **Composer no legado existe só para testes.** `index.php` não carrega o autoload;
> produção segue sem dependências. Os 38 testes de `tests/` são o **contrato** do
> motor: o port reproduz esses números. Se um deles falhar, o port está errado —
> não "ajuste" o teste. Rode com `vendor/bin/phpunit` (exige `algorithmia_test`).

**As duas suítes ficam verdes.** A CI roda ambas.

```bash
vendor/bin/phpunit                      # legado (MySQL)
cd platform && php artisan test         # port (PostgreSQL)
cd platform && vendor/bin/pint --test && vendor/bin/phpstan analyse
```

Leia [`docs/migracao/PLANO.md`](docs/migracao/PLANO.md) e
[`openspec/changes/migracao-laravel-postgresql/`](openspec/changes/migracao-laravel-postgresql/)
antes de mexer no port.

---

## 🖥️ Ambiente de PRODUÇÃO

**Uma VPS com root, rodando Docker** (confirmado pelo time em 2026-07-09). Não há
contradição com o que está escrito abaixo: é um host RHEL/AlmaLinux estilo cPanel,
mas **com root** — e por isso o Docker roda ali. O `httpd` do legado e os containers
do port convivem na mesma máquina durante a janela de corte.

### Legado — o que está no ar hoje

- **Código em:** `/home/algorithmia/public_html`
- **Servidor web:** **`httpd`** (NÃO é `apache2`). Reiniciar / limpar o cache de
  OPcache: `sudo systemctl restart httpd` (ou o botão de restart do painel).
  Comandos `apache2`/`a2ensite`/`ufw` **não existem** neste host.
- **Banco:** MySQL/MariaDB local. As credenciais vêm de variáveis de ambiente
  (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`). Em scripts CLI, passe-as **inline**
  no comando. **Nunca** imprima nem commite a senha (`config/db.local.php` e `.env`
  ficam fora do git).
- **Após QUALQUER alteração em arquivo `.php` no host, reinicie o `httpd`** — senão
  o OPcache continua servindo o código antigo.

> Dev local (Ubuntu/Apache, `php -S localhost:8001`) está documentado em
> [`docs/processo/DEPLOY.md`](docs/processo/DEPLOY.md); produção usa `httpd`.

### Port — Docker

nginx + php-fpm + PostgreSQL 18. Deploy por `bin/deploy.sh`, que recusa árvore suja,
tira um dump, migra, sobe, roda o smoke e **reverte sozinho se ele reprovar**. Nunca
suba os containers à mão. Runbook completo em
[`docs/operacao/RUNBOOK.md`](docs/operacao/RUNBOOK.md).

### ⚠️ Coexistência: o `httpd` já é dono da porta 80

Os dois não podem escutar a 80 ao mesmo tempo. Durante a janela de corte:

- O nginx do port publica numa porta alta (`ALGORITHMIA_PORTA`, padrão `8080`).
- O `httpd` continua atendendo o domínio e a TLS, e faz **proxy reverso** para essa
  porta quando o corte acontecer. Assim o certificado existente segue valendo.

Consequência que **quebra o login se for esquecida**: atrás do proxy, o Laravel
precisa saber que a conexão é HTTPS. Defina `TRUSTED_PROXIES` no `.env.producao` com
o IP do `httpd`. Sem isso, o app gera URLs `http://`, o cookie `secure` nunca é
enviado, e o jogador cai na tela de login para sempre.

Não coloque `TRUSTED_PROXIES=*` se o nginx atender direto: qualquer cliente forjaria
`X-Forwarded-Proto`. A variável é **vazia por padrão**, e está travada em
`platform/tests/Feature/ProxyReversoTest.php`.

Depois do corte, quando o legado for aposentado, o nginx pode assumir a 80/443 — e aí
`TRUSTED_PROXIES` volta a ficar vazio.

---

## 🤝 Coordenação na `main` (há outras pessoas trabalhando em paralelo)

- **SEMPRE** `git pull origin main` antes de começar e antes de commitar.
- Commits **pequenos e atômicos** (1 commit = 1 razão). **Conventional Commits em
  português**, no imperativo: `feat:`, `fix:`, `refactor:`, `docs:`, `test:`,
  `style:`, `perf:` (ex.: `fix: corrige cálculo de dano do especial`).
- **NÃO** faça `git push --force`, `git rebase` nem `git reset --hard` na `main`.
  Se houver conflito, **pare e avise o time**.
- Antes do push: `php -l` nos `.php` alterados; sem `var_dump`/`print_r`/
  `console.log`/`debugger` e sem código comentado morto.

---

## 🛑 Regras de ouro — NÃO QUEBRE

1. **NUNCA** rode `php database/migrate.php --reset` em produção: faz `DROP DATABASE`
   e **apaga contas, personagens e progresso de todos os jogadores**. O `migrate.php`
   **sem flag** é não-destrutivo (preserva dados); mesmo assim, prefira o seeder
   específico (abaixo) para mexer só em perguntas.
2. **NUNCA** edite o banco de produção à mão para "consertar" conteúdo. Conteúdo é
   versionado:
   - fases e desafios-base → `database/seeds.sql`
   - 157 perguntas ampliadas → `database/banco-questoes/*.php` (aplicadas pelo seeder
     **idempotente** `database/seed-banco-questoes.php`; há também o espelho SQL
     `database/banco-questoes.sql`).
3. **Mudança de schema** (coluna/ENUM novos) = **novo** arquivo
   `database/migrations/AAAAMMDD-descricao.sql`. O `migrate.php` aplica cada migration
   uma única vez (tabela `migracoes_aplicadas`). Nunca altere uma migration já
   aplicada — crie outra.
4. **`database/schema.sql` ignora o `DB_NAME` do ambiente.** As linhas 6 e 10 têm
   `CREATE DATABASE algorithmia` e `USE algorithmia` fixos. Rodar
   `DB_NAME=outro php database/migrate.php --reset` derruba `outro` e depois escreve
   em `algorithmia`. **Nunca** rode o migrador com `DB_NAME` customizado.
5. **No port, toda migration precisa ser ADITIVA** — criar tabela, criar coluna
   anulável, criar índice. Elas rodam contra o código velho ainda no ar, e o rollback
   de código não desfaz schema. Remover coluna = dois deploys.
6. Nada de `rm -rf`, `sudo` destrutivo, `chmod 777`. Não commite segredos.
7. Sem `any`/gambiarra que quebre as convenções do [`CLAUDE.md`](CLAUDE.md).

---

## ✅ Fatos já RESOLVIDOS — não "conserte" de novo

### O sorteio de perguntas

O **sorteio de perguntas da batalha já é aleatório e correto**:
`BatalhaService::sortearDesafios()` embaralha o pool da fase (`shuffle`), prioriza
perguntas inéditas via `respostas_log` e ordena por dificuldade. **Não altere essa
lógica.**

Se as perguntas "se repetirem", o problema é o **pool do banco estar pequeno** (host
sem as 157 perguntas) — **não o código**. Solução (idempotente, não-destrutiva):

```bash
DB_HOST=127.0.0.1 DB_NAME=algorithmia DB_USER=algorithmia DB_PASS='<senha>' \
  php database/seed-banco-questoes.php
```

Conferir (esperado ~9–14 perguntas por fase de combate, não 4–7):

```bash
mysql -u algorithmia -p algorithmia -e "SELECT fase_id, COUNT(*) FROM desafios GROUP BY fase_id;"
```

### Rejogar uma fase apaga a mancha da IA — de propósito

`ProgressoFase::registrar` só acumula o melhor resultado nas **estrelas**. `acertos`,
`erros` e `usou_ia` refletem a última partida. Parece bug; não é. É o que permite
reconquistar "Puro de Coração" depois de ter cedido ao Fragmento. **Redenção é regra
do jogo.** Está travado em teste nas duas bases.

### A auditoria em `docs/auditoria/` está DESATUALIZADA

É um retrato de 2026-06-18. Os dois débitos "críticos" que ela aponta —
CSRF-via-GET em `exigirCsrf` e ausência de transação no fluxo de recompensa — **já
foram corrigidos**. Leia o código antes de citá-la.

---

## 🚀 Deploy de atualização (fluxo padrão neste host)

```bash
cd /home/algorithmia/public_html
git pull origin main
# se houver migrations/seed novos (não-destrutivo, preserva dados):
DB_HOST=127.0.0.1 DB_NAME=algorithmia DB_USER=algorithmia DB_PASS='<senha>' \
  php database/migrate.php
sudo systemctl restart httpd
```

---

## 📚 Onde ler antes de agir

- [`CLAUDE.md`](CLAUDE.md) — convenções de código, nomenclatura, testes e git.
- [`docs/migracao/PLANO.md`](docs/migracao/PLANO.md) — o port: decisões, fases, riscos.
- [`docs/migracao/INVENTARIO.md`](docs/migracao/INVENTARIO.md) — mapa do domínio legado.
- [`docs/operacao/RUNBOOK.md`](docs/operacao/RUNBOOK.md) — deploy, rollback, backup, corte.
- [`openspec/changes/migracao-laravel-postgresql/`](openspec/changes/migracao-laravel-postgresql/) — proposta, design e specs do port.
- [`docs/processo/DEPLOY.md`](docs/processo/DEPLOY.md) — deploy do **legado** (`httpd`/cPanel).
- [`docs/desenvolvimento/`](docs/desenvolvimento/) — arquitetura, padrões, QA, releases.
- [`README.md`](README.md) — visão geral e setup.
