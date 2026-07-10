# Tasks — Migração para Laravel 13 + PostgreSQL

Todas concluídas, exceto o corte em produção (§6), que depende de credenciais e da
topologia descrita em [`RUNBOOK.md §9`](../../../docs/operacao/RUNBOOK.md).

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
- [~] 6.8 ~~Host confirmado~~ — **retratado**. A "confirmação" foi conversa, não comando. O
      host real (`server.tars.art.br`) é um **Virtualmin compartilhado** com seis tenants.
      Tem root, Docker 29.6 e `mod_proxy_http`, mas só ~950 MiB de RAM disponíveis — o
      `docker build` morreria de OOM. O usuário decidiu **subir uma VPS nova, do zero**;
      os requisitos dela estão no `RUNBOOK §0`. Nada de 6.10/6.11 se decide sem
      `bin/checar-host.sh`.
- [x] 6.9 `trustProxies` configurável por `TRUSTED_PROXIES`, vazio por padrão, travado em teste
- [x] 6.12 **Ensaio completo do corte (§8) em Docker local**, contra uma cópia do MySQL legado
      servida por um usuário só com `SELECT`. Achou três defeitos, todos exclusivos do
      *primeiro* deploy — os ensaios anteriores rodaram sobre volume já populado:
      `up -d` não esperava o `initdb`; o smoke era impossível de passar com o banco vazio
      (faltava repassar `--sem-conteudo`); e o §9 prescrevia um `TRUSTED_PROXIES` errado.
      Ver `RUNBOOK.md §6`, itens 6 a 8.
- [x] 6.13 **Segurança do legado, achada indo para o corte** — três defeitos, todos no
      caminho que o corte percorre:
      `migrate.php` chamava o seeder da conta de administrador incondicionalmente, e ele
      reescrevia a senha (para a que está publicada no repo) e apagava progresso,
      inventário e conquistas — **a cada deploy** (`fdb8364`);
      o `.htaccess` não bloqueava `tests/`, `bin/`, `vendor/`, `platform/` nem `worker/`,
      e o DocumentRoot é a raiz do projeto (`e4d9f9e`, `ab5db1d`);
      a importação abria o MySQL na rede — agora lê pelo socket Unix (`1f89eec`).
- [x] 6.14 `bin/checar-host.sh` (somente-leitura) e `RUNBOOK §0` com os requisitos de
      uma VPS nova. O script julga a RAM, e recusa responder onde não pode saber.
- [x] 6.15 **TLS direto** (`algorithmia-tls.conf`): numa VPS dedicada o nginx do port é
      dono da 80/443 e `TRUSTED_PROXIES` fica vazio. O §8/§9 pressupunham o `httpd` do
      legado na mesma máquina; sem ele o corte não tinha topologia. `RUNBOOK §10` descreve
      o corte para host novo — o rollback vira troca de DNS, e o legado nunca sai do ar.
      `deploy.sh`/`rollback.sh` leem `platform/.deploy/ambiente`, senão um rollback
      devolveria o site a HTTP na 8080 sem avisar.

- [x] 6.16 **Ensaio completo do §10** (corte para VPS dedicada), em Docker local: volume
      zerado → `bin/deploy.sh --sem-conteudo` lendo `.deploy/ambiente` → importação do
      MySQL efêmero → smoke completo → login, campanha e um turno de batalha por HTTPS,
      com `TRUSTED_PROXIES` **vazio**. E o rollback, que preservou a 443 — sem o
      `ambiente` o compose voltaria a `algorithmia.conf`, HTTP na 8080, calado.
- [x] 6.17 **Legado instalável do zero.** `schema.sql` fixava `CREATE DATABASE
      algorithmia; USE algorithmia;` e ignorava `DB_NAME`; corrigido isso, apareceu que
      as migrations rodavam antes do `seeds.sql` e o `INSERT` da seed morria em
      `Duplicate entry 'primeira_arma'`. A CI era cega: montava o banco com um `sed` no
      schema e nunca chamava o migrador. `config/db.php` também assumia `dev` quando
      `APP_ENV` faltava — e o vhost de produção não a define.

## 7. Fase 8 do roteiro v1 — segurança, observabilidade e operação
- [x] 7.1 **Rate limiting**: `/entrar` aceitava força bruta. Dois limites (5/min por
      e-mail+IP; 20/min por IP, contra password spraying, que o primeiro não vê).
- [x] 7.2 **CSP com nonce**, emitido pelo PHP — o nginx não pode conhecer o nonce. Os
      quatro `onsubmit="return confirm()"` viraram `data-confirmar` + JS externo, porque
      nonce não alcança atributo de evento.
- [x] 7.3 **Fontes da marca hospedadas por nós.** O teste de recursos externos revelou
      que o port só as carregava na tela de lore: rodava com fonte de sistema em todas as
      outras. Regressão visual silenciosa, e o IP de alunos menores ia para o Google.
- [x] 7.4 **Trilha de auditoria** (migration aditiva). Sem FK para alvo nem autor, sem
      `updated_at`. O `resumo` da exclusão de fase conta os desafios da cascata ANTES do
      delete. O gabarito não entra.
- [x] 7.5 **Logs estruturados** em JSON com `request_id`, `usuario_id`, `ip` e `rota`. O
      mesmo id sai no `X-Request-Id` e na auditoria, e nunca vem do cliente.
- [x] 7.6 Checklist v1 §8, itens que se aplicam: **restauração testada** (`restore.sh
      --ensaio`), **sem segredos no repositório** (varredura em todo o histórico: só
      placeholders `CHANGE_ME`), **sem credenciais padrão** (ver 6.13).
- [ ] 7.7 Métricas e alertas — dependem de um host para onde exportá-las.

## 8. Multitenancy (Fases 2, 3, 5 e 9 do roteiro v1) — **CONCLUÍDA**

> Decidido em 2026-07-10: fazer **depois** da Fase 8. Exigiu proposta própria —
> [`fundacao-multitenant`](../fundacao-multitenant/) —, e ela está **inteira**: Etapas A a E,
> mais as correções F e G. O corte tinha de acontecer antes ou depois dela, nunca no meio.
>
> **A ordem foi invertida ao construir:** `tenant_id` entrou nas 13 tabelas **antes** do
> corte, e não depois. A regra dos três deploys é regra de *coexistência*, e antes do corte
> não há código velho no ar com que coexistir. Ver `fundacao-multitenant/design.md §4`.
>
> A Fase 3 do roteiro (`content_packages`) **não entrou**, de propósito: cada escola nova
> recebe uma cópia editável do mundo. Conteúdo *diferente* por instituição merece proposta
> própria.

### Bloqueado na VPS nova (o usuário vai provisioná-la do zero)
- [ ] 6.10 `bash bin/checar-host.sh` no host novo; `.env.producao` com `TRUSTED_PROXIES`
      **medido, não copiado**; legado em somente leitura
- [ ] 6.11 `bin/deploy.sh --sem-conteudo` → importar pelo socket → `algorithmia:smoke` →
      vhost do `httpd` como proxy reverso → janela de coexistência. Ver `RUNBOOK §8`,
      cujo passo 0 (pôr o banco do legado em dia) não pode ser pulado.
