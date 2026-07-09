# AGENTS.md — Guia para agentes de IA (Codex e afins)

Instruções obrigatórias para qualquer agente de codificação que trabalhe neste
repositório. Complementa o [`CLAUDE.md`](CLAUDE.md) (convenções de código, git,
testes e segurança) e os docs em [`docs/desenvolvimento/`](docs/desenvolvimento/).
**Leia antes de agir. Em dúvida sobre algo destrutivo ou que afete dados de
jogadores: PARE e pergunte ao time.**

O projeto é o **Algorithmia** — jogo educativo de RPG em **PHP puro** (MVC
artesanal) + **PDO/MySQL**, sem dependências externas em produção: `index.php`
não carrega o autoload do Composer.

> **Composer existe, mas só para testes.** `composer.json` traz o PHPUnit como
> dependência de desenvolvimento, usada pelos testes de caracterização em
> `tests/` que travam o comportamento do motor antes da migração para Laravel.
> Rode com `vendor/bin/phpunit` (exige o banco `algorithmia_test`).
> Ver [`docs/migracao/PLANO.md`](docs/migracao/PLANO.md).

---

## 🖥️ Ambiente de PRODUÇÃO (VPS)

- **Host:** RHEL/AlmaLinux estilo cPanel.
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
4. Nada de `rm -rf`, `sudo` destrutivo, `chmod 777`. Não commite segredos.
5. Sem `any`/gambiarra que quebre as convenções do [`CLAUDE.md`](CLAUDE.md).

---

## ✅ Fato já RESOLVIDO — não "conserte" de novo

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
- [`docs/processo/DEPLOY.md`](docs/processo/DEPLOY.md) — deploy (com a nota de `httpd`/cPanel).
- [`docs/desenvolvimento/`](docs/desenvolvimento/) — arquitetura, padrões, QA, releases.
- [`README.md`](README.md) — visão geral e setup do banco.
