# Tasks — Migração para Laravel 13 + PostgreSQL

Todas concluídas, exceto o corte em produção (§6), que depende de credenciais e da
contradição registrada em [`RUNBOOK.md §0`](../../../docs/operacao/RUNBOOK.md).

## 1. Rede de segurança (sobre o legado, em PHP puro)
- [x] 1.1 `composer.json` + PHPUnit 12 como dependência **só de desenvolvimento** (`index.php` não carrega o autoload)
- [x] 1.2 `tests/bootstrap.php` aborta se `DB_NAME` não terminar em `_test`
- [x] 1.3 `BatalhaService::sortearDesafios` de `private` a `protected` — a única mudança em produção
- [x] 1.4 38 vetores-ouro: dano, combo, especial, escudo, poção, Fragmento, morte súbita, XP, estrelas, reputação, conquistas, anti-repetição, não-vazamento do gabarito
- [x] 1.5 Mutações deliberadas provam que a rede morde

## 2. Núcleo Laravel
- [x] 2.1 Laravel 13.19 em `platform/`, PostgreSQL 18 via `docker compose`
- [x] 2.2 Migrations das 13 tabelas; `SchemaTest` trava cada tradução
- [x] 2.3 `/healthz` devolve 503 quando o banco cai
- [x] 2.4 Pint, Larastan nível 6, PHPUnit; CI com dois jobs (legado/MySQL e port/PostgreSQL)
- [x] 2.5 `CompatibilidadeDeSenhaTest`: o `password_hash()` do legado é bcrypt e o `Hash::check` valida → sem redefinição de senha no corte

## 3. Motor
- [x] 3.1 `Dominio/Combate/`: `MotorDeBatalha`, `EstadoDeBatalha`, `CorretorDeRespostas`, `SorteioAntiRepeticao`, `BatalhaEmMemoria`
- [x] 3.2 `Dominio/Progressao/`: `ServicoDe{Progressao,Reputacao,Conquistas,Recompensa,Maestria,Missoes,Regioes,Onboarding}`
- [x] 3.3 `config/jogo.php` como fonte única do balanceamento; `config/bestiario.php` como cânone
- [x] 3.4 Os 38 vetores-ouro reescritos contra o motor novo — **os mesmos números**
- [x] 3.5 Migration `recompensas_batalha`: idempotência da recompensa por chave no banco

## 4. Dados
- [x] 4.1 Conexão `legado` (somente leitura); a importação nunca escreve no MySQL
- [x] 4.2 `algorithmia:importar` com `--dry-run` (transação + rollback) e `--truncar`
- [x] 4.3 IDs preservados; `fases` em duas passadas (auto-referência); `setval` nas sequências
- [x] 4.4 Reconciliação por tabela + verificações que a contagem não pega (IDs secundários, Fragmento, requisito órfão)
- [x] 4.5 `ImportacaoDoLegadoTest` exercita o pipeline contra um legado de mentira em SQLite (a CI não tem MySQL)

## 5. Web
- [x] 5.1 Rotas nomeadas; **toda escrita é POST** (11 rotas, todas com teste de 405 no GET)
- [x] 5.2 Auth + CSRF do Laravel; sessão em driver `database`
- [x] 5.3 Blade para as 17 telas; `public/js/batalha.js` reaproveitado sem alteração
- [x] 5.4 Painel do Mestre com validação por tipo de desafio (um `ordenar` sem opções é insolúvel)
- [x] 5.5 Splash, lore, os três finais, bestiário na arena, chip de tática no mapa, Poder Total na loja

## 6. Operação e corte
- [x] 6.1 Imagem de produção; `.dockerignore` derruba o contexto de 2,5 GB para 1 KB
- [x] 6.2 O entrypoint recusa `APP_KEY` vazio e `APP_DEBUG=true` em produção
- [x] 6.3 `bin/deploy.sh` (dump → build → migrate → smoke → rollback automático), `bin/rollback.sh`
- [x] 6.4 `bin/backup.sh` e `bin/restore.sh --ensaio` (restaura num banco descartável)
- [x] 6.5 `algorithmia:smoke` joga uma fase real numa transação e a desfaz
- [x] 6.6 CI ganha job que constrói a imagem e prova as guardas do entrypoint
- [x] 6.7 `docs/operacao/RUNBOOK.md`; deploy, rollback e backup exercitados localmente
- [ ] 6.8 **Resolver a contradição de host** (`AGENTS.md` diz cPanel/`httpd`; o port pressupõe Docker)
- [ ] 6.9 Provisionar a VPS, `.env.producao`, pôr o legado em somente leitura
- [ ] 6.10 Importar, `bin/deploy.sh`, apontar o DNS, janela de coexistência
