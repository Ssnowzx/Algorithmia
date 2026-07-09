# Plano de Migração — Algorithmia → Laravel 13 + PostgreSQL

**Versão:** 2.0 · **Data:** 2026-07-08
**Substitui:** `algorithmia_estrategia_migracao_laravel_postgresql_multitenant.html` (v1.0)

---

## 1. Decisões que definem este plano

Quatro respostas moldaram tudo o que vem abaixo:

| Decisão | Escolha | Consequência |
|---|---|---|
| Restrição acadêmica | Cumprida — Composer liberado | Laravel é opção legítima; o `AGENTS.md` precisa ser atualizado |
| Alvo | **Portar o Algorithmia** | Mesmo jogo, mesmo conteúdo, mesmos 5 mestres |
| Multitenancy | **Adiada** | Sem `tenant_id`, sem RLS, sem billing, sem feature flags |
| Infraestrutura | VPS com root | Docker e PostgreSQL viáveis; Redis opcional |

A consequência mais importante: **o RLS existia para servir a multitenancy.** Adiada a
segunda, cai a primeira. Metade do roteiro v1.0 sai de cena.

---

## 2. Por que o roteiro v1.0 foi reescrito

O roteiro original era sólido nos princípios — domínio antes de interface, `tenant_id`
nunca vindo do cliente, gabarito só no servidor, monólito modular sem microserviços
prematuros — e desalinhado no escopo e na sequência. Cinco correções:

**A rede de segurança era prosa, não teste.** A Fase 0 proibia tocar em código de
produção e entregava Markdown. A Fase 4 exigia "testes de equivalência funcional em
relação às regras legadas". Não havia contra o que comparar: o repositório tinha zero
testes. Corrigido — ver §4, Fase 0, que já está entregue.

**A pilha era maior que a necessidade.** Horizon e Reverb são daemons de vida longa
que existem para filas e websockets. O jogo não tem fila nem tempo real. Entram quando
houver trabalho para eles.

**O RLS descrito podia não fazer nada.** No PostgreSQL, o dono da tabela ignora as
policies a menos que se declare `FORCE ROW LEVEL SECURITY`, e um papel com `BYPASSRLS`
passa direto. Nenhum dos dois aparecia no documento. Fica registrado aqui para quando
a multitenancy voltar (§6).

**Faltava a conformidade que o próprio plano de negócio exige.** O `Xiax-Plano-de-Produto`
v1.1 diz ter integrado "revisão de conformidade jurídica (LGPD e leis de dados de
menores)", e o público-alvo é Fundamental II e Médio. As palavras `lgpd`, `menor` e
`consentimento` não apareciam uma única vez no roteiro técnico. Fora de escopo agora,
mas é bloqueador da Xiax — não da migração.

**Algorithmia e Xiax são produtos diferentes.** O mapeamento `mestres → mentors`,
`fases → stages` preservava a forma de um curso de programação. A Xiax promete
converter o currículo da escola, alinhado à BNCC. O ativo reaproveitável é o **motor**;
as 157 questões de PHP e SQL não servem a um nono ano. Este plano porta o Algorithmia
e deixa a generalização para depois, quando existir escola interessada.

---

## 3. Arquitetura-alvo

```
┌─────────────────────────────────────────┐
│  Blade + JS vanilla (as views atuais)   │
└──────────────────┬──────────────────────┘
                   │
┌──────────────────▼──────────────────────┐
│              LARAVEL 13                 │
│  Auth · Policies · Controllers · Blade  │
│                                         │
│  app/Dominio/                           │
│    Combate | Progressao | Conteudo      │
└──────────────────┬──────────────────────┘
                   │
          ┌────────▼────────┐
          │   PostgreSQL    │
          └─────────────────┘
```

Sessão em driver `database` (não em cookie — ver §5, risco 2). Cache em arquivo.
Sem Redis, sem filas, sem websockets, até que exista uma necessidade real.

---

## 4. Fases

Cada fase só avança quando o critério de aceite passa.

### Fase 0 — Rede de segurança ✅ **ENTREGUE**

Testes de caracterização sobre o **legado**, em PHP puro. São o contrato que o port
terá de reproduzir número a número.

- `composer.json` + PHPUnit 12 como dependência **só de desenvolvimento**.
  `index.php` não carrega o autoload do Composer; produção segue sem dependências.
- Banco `algorithmia_test`, isolado. `tests/bootstrap.php` aborta se `DB_NAME` não
  terminar em `_test` — os `TRUNCATE` de `Mundo::limpar()` nunca alcançam o banco real.
- Um único toque em produção: `BatalhaService::sortearDesafios` passou de `private` a
  `protected`, para o teste poder injetar uma sequência fixa de desafios. Zero mudança
  de comportamento.
- 38 testes, 134 asserções: dano, combo e seu teto, especial, escudo, poção com teto de
  cura, Fragmento da IA, morte súbita e fúria, vitória, derrota, XP, nível, ouro,
  estrelas, reputação, conquistas de região, anti-repetição do sorteio, e o
  não-vazamento do gabarito.

Os valores esperados são **derivados à mão** das constantes de `config/config.php`, não
capturados de um snapshot. Se uma regra mudar de propósito, o teste muda junto e o diff
mostra a decisão.

**Critério de aceite:** `vendor/bin/phpunit` verde; banco de desenvolvimento intocado.

### Fase 1 — Núcleo Laravel

Aplicação nova em diretório separado, sem regra de jogo.

- Laravel 13 (requer PHP 8.3+; a VPS roda 8.4), PostgreSQL 18.
- `docker compose` para app, PostgreSQL e Mailpit. Sem Redis.
- Pint, Larastan, Pest ou PHPUnit, CI em GitHub Actions, health check.
- Migrations traduzindo o schema: os **9 ENUMs** viram `CHECK` ou tabelas de domínio,
  `AUTO_INCREMENT` vira `GENERATED … AS IDENTITY`, `TINYINT(1)` vira `boolean`,
  `ON UPDATE CURRENT_TIMESTAMP` vira trigger ou é resolvido no Eloquent, `JSON` vira
  `jsonb`.

**Critério de aceite:** ambiente sobe com um comando; CI verde; nenhuma credencial no repositório.

### Fase 2 — Port do motor

O ativo real. Portar `BatalhaService`, `RecompensaService`, `ProgressaoService`,
`ReputacaoService`, `ConquistaService` como domínio testável, sem depender de HTTP.

Os vetores-ouro da Fase 0 são reescritos em Pest contra o novo motor. **Os mesmos
números.** Divergência é bug do port, não licença para "melhorar o balanceamento".

Duas dívidas se pagam aqui, porque o port as expõe:

1. **Idempotência da recompensa.** Hoje a proteção contra duplo-crédito é o flag
   `recompensado` em `$_SESSION['batalha']`. `RecompensaService::conceder` chamado duas
   vezes duplica a reputação — XP e ouro escapam só por acidente, porque são gravados
   como valor absoluto a partir de uma linha lida antes. Está travado em
   `tests/Motor/RecompensaTest.php::conceder_duas_vezes_duplica_a_reputacao_a_guarda_vive_na_sessao`.
   No port isso vira chave de idempotência no banco.
2. **IDs de fase hardcoded.** `ConquistaService.php:86` traz `$secundarias = [8, 14, 20, 32]`.
   O port precisa preservar os IDs ou a conquista `arquivista_do_vazio` morre em silêncio.

**Critério de aceite:** os vetores-ouro passam nos dois motores, com os mesmos valores.

### Fase 3 — Dados e conteúdo

Não há dado precioso: o conteúdo mora em `database/seeds.sql` e
`database/banco-questoes/*.php`. O que existe de real é um punhado de contas.

- Seeder Laravel para mestres, fases, desafios, itens, diálogos, conquistas —
  **preservando os IDs** (ver Fase 2, dívida 2).
- Comando de importação das contas e do progresso do MySQL, com `--dry-run` e relatório
  de reconciliação. Senhas: os hashes `password_hash()` do PHP são compatíveis com o
  `Hash::check` do Laravel (bcrypt) — não é preciso resetar ninguém.

**Critério de aceite:** contagens reconciliadas por entidade; nenhuma migração roda no deploy.

### Fase 4 — Web

As views já são PHP com HTML: o port para Blade é quase mecânico. O CSS e o JS vanilla
seguem como estão.

- Rotas nomeadas substituem o `?url=controller/metodo/param`.
- Middleware de auth e o CSRF do Laravel substituem `Auth` e `Controller::exigirCsrf`.
  Isso fecha de graça o furo que o inventário achou: **`historia/concluir` grava
  progresso e XP via GET, sem CSRF** (`docs/migracao/INVENTARIO.md`, §1). Os cinco
  endpoints AJAX de batalha validam token, mas não exigem POST.
- Sessão em driver `database`. O estado da batalha carrega o gabarito de todos os
  desafios e não cabe num cookie.

**Critério de aceite:** um aluno entra, cria personagem, joga uma fase, vê o feedback,
conclui e consulta o progresso — no frontend novo.

### Fase 5 — Corte

- Provisionamento da VPS, deploy, smoke tests.
- O legado fica de pé, em leitura, por uma janela combinada.
- Plano de rollback escrito e testado antes do corte, não depois.

**Critério de aceite:** rollback exercitado ao menos uma vez em ambiente de teste.

---

## 5. Riscos, com evidência

Extraídos do código, não de suposição. Detalhe completo em `INVENTARIO.md`.

| Risco | Evidência | Controle |
|---|---|---|
| Recompensa duplicada | `RecompensaService.php:19` — a guarda vive na sessão | Chave de idempotência no banco (Fase 2) |
| Estado de batalha estoura o cookie | `$_SESSION['batalha']` guarda o gabarito de N desafios | Driver `database` (Fase 4) |
| Conquista morre em silêncio | `ConquistaService.php:86` — IDs de fase fixos | Preservar IDs no seeder (Fase 3) |
| `lastInsertId()` sem sequência | `Model::create` | Eloquent resolve; validar no port |
| `ON DUPLICATE KEY UPDATE` | `ProgressoFase::registrar`, `Inventario::adicionar` | `ON CONFLICT` no PostgreSQL |
| `INSERT IGNORE` + `rowCount()` | Detecção de conquista inédita | `ON CONFLICT DO NOTHING RETURNING` |
| E-mail duplicado | `utf8mb4_unicode_ci` é case-insensitive; PostgreSQL não é | `citext` ou normalizar no cadastro |
| Escrita via GET | `historia/concluir` (`INVENTARIO.md` §1) | CSRF do Laravel (Fase 4) |

### Armadilha achada durante a Fase 0

`database/schema.sql:6,10` faz `CREATE DATABASE IF NOT EXISTS algorithmia` e
`USE algorithmia`. O `DB_NAME` do ambiente é **ignorado**. Rodar
`DB_NAME=outro php database/migrate.php --reset` derruba `outro` e depois escreve em
`algorithmia`. Não corrigimos — o legado será aposentado — mas ninguém deve rodar o
migrador com `DB_NAME` customizado.

---

## 6. O que fica fora, e quando volta

| Adiado | Volta quando | Custo de adiar |
|---|---|---|
| `tenant_id` no schema | Existir a instituição nº 2 | **Alto** — retrofitar coluna em 13 tabelas |
| RLS | Junto com `tenant_id` | Baixo — é camada extra sobre o `tenant_id` |
| Redis, filas, Horizon | Houver job assíncrono real | Baixo |
| Websockets (Reverb) | Houver funcionalidade em tempo real | Baixo |
| Billing, feature flags | A Xiax vender | Baixo |
| LGPD, consentimento parental | **Antes** de qualquer aluno menor de idade entrar | Bloqueador da Xiax |

Uma ressalva honesta sobre a primeira linha: `tenant_id` é barato de incluir agora e
caro de retrofitar depois. Se houver expectativa concreta de uma segunda instituição
em menos de um ano, vale adicionar a coluna e um escopo global na Fase 1 — sem RLS,
sem billing, sem resolução por domínio. É uma coluna e um trait, não um subsistema.

Quando a multitenancy voltar, dois detalhes que o roteiro v1.0 omitia e que fazem o
RLS funcionar de verdade:

- O papel da aplicação **não pode ser dono das tabelas** nem ter `BYPASSRLS`; donos
  ignoram policies a menos que a tabela declare `FORCE ROW LEVEL SECURITY`.
- `SET LOCAL app.tenant_id` vale por transação. Com `pgbouncer` em modo transaction, o
  contexto se perde entre statements fora de transação explícita.

---

## 7. Como rodar os testes

```bash
composer install
mysql -u root -e "CREATE DATABASE IF NOT EXISTS algorithmia_test \
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
# schema.sql tem CREATE DATABASE/USE hardcoded — remova as duas primeiras instruções:
sed -e '/^CREATE DATABASE IF NOT EXISTS algorithmia/,+2d' -e '/^USE algorithmia;/d' \
  database/schema.sql | mysql -u root algorithmia_test

vendor/bin/phpunit
```

O bootstrap recusa qualquer `DB_NAME` que não termine em `_test`.
