# Plano de Migração — Algorithmia → Laravel 13 + PostgreSQL

**Versão:** 2.0 · **Data:** 2026-07-08
**Substitui:** [`roteiro-v1.html`](roteiro-v1.html) — o roteiro original de 10 fases, guardado como registro.

> 🏁 **O código está pronto.** O placar final das dez fases, o que divergiu do roteiro e por quê,
> e os sete defeitos que só apareceram contra dados reais estão em
> [`roteiro-v2-concluido.html`](roteiro-v2-concluido.html). **O corte em produção não foi
> feito** — ele é operação, e mora no [`RUNBOOK §10`](../operacao/RUNBOOK.md).

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

### Fase 1 — Núcleo Laravel ✅ **ENTREGUE**

Aplicação nova em `platform/`, sem nenhuma regra de jogo.

- Laravel 13.19 sobre PHP 8.5 (o framework exige 8.3+; a VPS roda 8.4).
- `docker compose up -d` sobe PostgreSQL 18.4, um segundo PostgreSQL só para
  testes (em `tmpfs`, porta 55433) e o Mailpit. Sem Redis. A aplicação roda no
  host via `php artisan serve` — containerizá-la só rende algo no deploy (Fase 5).
- Pint, Larastan em **nível 6** e PHPUnit. Ficamos no PHPUnit em vez do Pest
  porque `pest-plugin-laravel` ainda não suporta o Laravel 13 — e porque os
  vetores-ouro da Fase 0 já são PHPUnit, então portam quase literalmente.
- CI com dois jobs: os vetores-ouro do legado (MySQL) e o port (PostgreSQL).
  **Os dois precisam ficar verdes durante todo o port.**
- `/healthz` responde 503 quando o banco cai. O `/up` que o Laravel monta sozinho
  prova apenas que o PHP está de pé.
- As 13 tabelas traduzidas, com os testes que travam a tradução
  (`platform/tests/Feature/SchemaTest.php`): os 9 ENUMs viraram `CHECK`,
  `TINYINT(1)` virou `boolean`, `JSON` virou `jsonb`, e o UNIQUE de e-mail virou um
  índice sobre `lower(email)` — sem a extensão `citext`, que exigiria superusuário
  no deploy.

A suíte do port roda contra **PostgreSQL de verdade**, não contra SQLite em
memória. Um SQLite esconderia exatamente o que a migração precisa expor.

Três coisas que só apareceram porque rodamos de verdade:

1. O `postgres:18` mudou a convenção do volume: o mount vai em `/var/lib/postgresql`,
   e não mais em `.../data`. Com o caminho antigo o container sobe e morre.
2. No PostgreSQL, um statement que falha **aborta a transação inteira** (SQLSTATE
   `25P02`). Teste que espera mais de uma violação de constraint precisa de savepoint.
   O MySQL era leniente.
3. `Auth::attempt` regrava a senha quando o custo do bcrypt gravado difere do
   configurado, e escreve na coluna de `getAuthPasswordName()` — `password` por
   padrão. Sem sobrescrever isso para `senha_hash`, o login quebra na importação.

E uma boa notícia para a Fase 3, agora provada por teste
(`platform/tests/Feature/CompatibilidadeDeSenhaTest.php`): o `password_hash()` do
legado é bcrypt e o `Hash::check` do Laravel o valida. **Ninguém precisa redefinir
senha no corte.**

**Critério de aceite:** atingido — `docker compose up -d` + `php artisan migrate`;
Pint, PHPStan nível 6 e 12 testes verdes; nenhuma credencial versionada.

### Fase 2 — Port do motor ✅ **ENTREGUE**

O ativo real, agora em `platform/app/Dominio/`, sem depender de HTTP nem de sessão.

- `Combate/`: `MotorDeBatalha`, `EstadoDeBatalha`, `CorretorDeRespostas`,
  `SorteioAntiRepeticao`.
- `Progressao/`: `ServicoDeProgressao`, `ServicoDeReputacao`, `ServicoDeConquistas`,
  `ServicoDeRecompensa`.
- `config/jogo.php` passa a ser a fonte única do balanceamento.

O motor ganhou **duas costuras**, e só duas: onde a batalha é guardada
(`RepositorioDeBatalha` — sessão em produção, memória nos testes) e como as
perguntas são sorteadas (`SorteadorDeDesafios`). Ambas existem porque são o que
impede um teste de medir a aritmética do combate: uma exige servidor web, a outra
é o Mt19937 do PHP. Tudo o mais é regra, e regra é testada.

Os 38 vetores-ouro foram reescritos contra o motor novo, e passam com **os mesmos
números**. Somaram-se 2 casos, e um mudou de propósito (abaixo). Para confirmar
que a rede não é decorativa, duas mutações deliberadas em `config/jogo.php`:
alterar `combo_bonus` de 0.25 para 0.30 quebra 3 testes; alterar `rage_max` de 4.0
para 5.0 quebra 1.

**A dívida da idempotência foi paga.** No legado, a guarda contra duplo-crédito é
o flag `recompensado` de `$_SESSION['batalha']`: dura o que dura a sessão, e não
vale nada contra duas requisições concorrentes. `RecompensaService::conceder`
chamado duas vezes duplica a reputação — XP e ouro escapam só por acidente, por
serem gravados como valor absoluto de uma linha lida antes. Agora cada batalha
nasce com um `batalhaId` gerado no servidor, e a concessão o insere em
`recompensas_batalha` antes de creditar qualquer coisa; a chave primária serializa
a disputa no banco, não na aplicação. É a **única divergência deliberada** em
relação ao legado, e está registrada nos dois lados:

| Legado | Port |
|---|---|
| `conceder_duas_vezes_duplica_a_reputacao_a_guarda_vive_na_sessao` | `conceder_a_mesma_batalha_duas_vezes_nao_credita_de_novo` |

Um detalhe que o port quase perdeu: `ProgressoFase::registrar` só acumula o melhor
de sempre nas **estrelas**. `acertos`, `erros` e `usou_ia` refletem a última
partida. Isso não é descuido — rejogar uma fase limpa apaga a mancha do Fragmento
da IA, e é o que permite reconquistar "Puro de Coração" depois de ter cedido.
Redenção é regra do jogo. Está travado em
`rejogar_uma_fase_mantem_as_melhores_estrelas_mas_limpa_a_marca_da_ia`.

Fica pendente para a Fase 3: `ConquistaService` referencia as fases secundárias por
ID fixo (`[8, 14, 20, 32]`, agora em `config('jogo.fases_secundarias')`). O seeder
**precisa preservar os IDs originais**, ou `arquivista_do_vazio` morre em silêncio.

**Critério de aceite:** atingido — 40 vetores-ouro verdes no port, 38 no legado,
PHPStan nível 6 limpo.

### Fase 3 — Dados e conteúdo ✅ **ENTREGUE**

Um único comando, `php artisan algorithmia:importar`, copia o MySQL legado para o
PostgreSQL. Não há seeder separado para o conteúdo: reescrever `seeds.sql` em PHP
criaria um segundo cânone para manter, e o banco legado já é a verdade.

- Conexão `legado` (somente leitura). A importação **nunca escreve no MySQL** — o
  jogo antigo precisa seguir de pé durante a coexistência, e o rollback depende disso.
- `--dry-run` roda a importação inteira dentro de uma transação e a desfaz. Um
  ensaio que não exercita chaves estrangeiras nem conversão de tipos não prova nada.
- `--truncar` para substituir; sem ele, o comando **recusa** sobrescrever um destino
  com dados.
- Relatório de reconciliação por tabela, mais três verificações que uma contagem
  igual não pegaria: IDs das fases secundárias preservados, Fragmento da IA presente
  no catálogo, nenhuma fase com requisito órfão.
- `TINYINT(1)` → `boolean` e `JSON` → `jsonb` na travessia. `fases` é copiada em duas
  passadas por causa da auto-referência `requisito_fase_id`.
- Sequências reposicionadas com `setval` ao final: os IDs vieram explícitos, então
  elas continuariam em 1 e o próximo `INSERT` colidiria.

Executado contra o banco real: **1.306 linhas em 13 tabelas**, reconciliadas —
13 usuários, 10 personagens, 5 mestres, 35 fases, 955 desafios, 132 diálogos,
18 itens, 20 conquistas, 20 linhas de inventário, 15 de progresso, 72 respostas,
11 conquistas de personagem, 0 escolhas.

Verificado sobre os dados importados, e não só sobre fixtures:

- Os 955 gabaritos ficaram tipados em `jsonb` — 393 array, 163 booleano, 399 numérico.
- A conta `masterboss@boss.com` autentica com a senha original. **Ninguém precisa
  redefinir senha.**
- O motor portado **venceu uma fase real em dois turnos** (Porto da Sintaxe,
  "Variáveis e Eco", contra o Slime de Variável), sorteando 9 desafios do pool com
  limite de ritmo 4, sem vazar gabarito para o cliente.

A CI não tem o MySQL do legado, então `tests/Feature/ImportacaoDoLegadoTest.php`
exercita o pipeline contra um legado em miniatura em SQLite. O dialeto de origem não
é o ponto: o que se testa é a preservação de IDs, a conversão de tipos, o ensaio que
não grava, a recusa em sobrescrever, a auto-referência das fases, e o aborto com
rollback quando o legado tem referência órfã.

**Critério de aceite:** atingido — contagens reconciliadas; nenhuma migração roda no
deploy; a importação é um comando explícito.

### Fase 4 — Web ✅ **ENTREGUE (vertical do aluno)**

Rotas nomeadas no lugar de `?url=controller/metodo/param`. Auth e CSRF do Laravel no
lugar de `Auth` e `Controller::exigirCsrf`. Sessão em driver `database` — o estado da
batalha carrega o gabarito de todos os desafios e não caberia num cookie.

O `public/js/batalha.js` do legado foi reaproveitado **sem uma linha de alteração**: ele
já era parametrizado por `window.BATALHA = {csrf, estado, urls}`. Só as URLs mudaram de
forma. O CSS também é o mesmo. O port não é uma oportunidade para redesenhar a
identidade visual.

Os dois furos que o inventário achou estão fechados, e travados em teste:

- **`historia/concluir` gravava progresso e XP por GET.** Agora é POST; o GET devolve
  405. Bastava um `<img src>` numa página qualquer para avançar a campanha de quem
  apenas a abriu.
- **Os cinco endpoints AJAX de batalha validavam o token mas aceitavam qualquer
  método.** Agora são POST. `nenhum_endpoint_de_turno_aceita_get` cobre os cinco.

Três defeitos apareceram construindo:

1. `GET /batalha/{fase}` engolia `GET /batalha/responder` e tentava carregar a fase de
   id `"responder"` — 500 em vez de 405. Resolvido com `whereNumber('fase')`.
2. O `bootstrap/app.php` do skeleton traz `shouldRenderJsonWhen($request->is('api/*'))`.
   Como os turnos vivem sob `/batalha`, **uma sessão expirada devolveria uma página
   HTML no meio de um `fetch()`**, e o `batalha.js` quebraria ao interpretá-la como
   JSON. Passou a ser `|| $request->expectsJson()`.
3. O jogo não destravava. A fase 1 é do tipo `historia`, a 2 a exige, e o
   `HistoriaController` não existia. Portado (só `ver` e `concluir`), a campanha anda.

Verificado por HTTP de verdade, contra o servidor e os dados importados: login como
`masterboss@boss.com` com a senha original, cena do prólogo, conclusão por POST, fase 2
destravada, arena contra o Slime de Sintaxe, um turno por AJAX. O gabarito não aparece
no HTML da arena nem no payload do turno.

**Critério de aceite:** atingido para a vertical do aluno — entra, cria personagem,
joga, recebe feedback, conclui e consulta o progresso, no frontend novo.

### Fase 4b — Telas restantes ✅ **ENTREGUE**

Loja, Inventário, Ranking, Painel do Mestre, a página de lore e a sequência dos três
finais. **O port da camada web está completo.**

Toda escrita virou POST. No legado, além do `historia/concluir` já citado,
`loja/vender/5` transformava um item em ouro, `inventario/descartar/5` o destruía para
sempre e `mestre/excluirFase/5` apagava a fase e seus desafios em cascata — todas por
GET, todas alcançáveis por um `<img src>`. `SegurancaWebTest` agora percorre as onze
rotas de escrita e exige 405 em cada uma.

Regras de jogo que o port precisou preservar, e que hoje têm teste:

- Revenda por metade do preço, e **o Fragmento da IA não se vende**: transformá-lo em
  ouro faria da queda moral um negócio.
- Equipar desequipa o mesmo slot. Sem isso, duas espadas somariam bônus.
- Os objetivos da loja (`primeira_arma`, `arsenal_completo`, `primeira_pocao`) pagam
  ouro uma única vez — a idempotência vem da chave primária composta das conquistas.
- A escolha diante da IA Ancestral pesa mais que a reputação, **mas não a apaga**: quem
  manda destruir o Fragmento sem nunca ter recusado sua ajuda recebe o final de
  equilíbrio, não o de mestre.
- A validação por tipo de desafio no Painel do Mestre não é burocracia: um `ordenar` sem
  opções deixa o jogador sem nada para mover, e um índice de gabarito fora da faixa faz
  o corretor recusar a resposta certa **para sempre**. O legado salvava os dois.

Dois números mágicos do legado foram derivados dos dados em vez de repetidos:
`HistoriaController` fixava a fase final no id 35 e a véspera na `ordem_global` 34. Agora
o confronto se declara pelo `tipo = 'chefe_final'`, e a véspera pergunta quem é a próxima
fase. Um seeder diferente teria deixado a tela de finais inalcançável sem nada acusar.

Duas armadilhas do Laravel evitadas:

1. Com `?Desafio $desafio = null` num controller, o Laravel resolve a dependência pelo
   container e injeta um **model vazio** no lugar de `null` — criar viraria atualizar um
   registro inexistente. Criar e editar são métodos separados.
2. `GET /mestre/desafios/novo` casaria com `/mestre/desafios/{desafio}` e procuraria o
   desafio de id `"novo"`. As rotas literais vêm antes, e o curinga é `whereNumber`.

A `lore.php` (298 linhas) foi convertida mecanicamente: suas únicas expressões PHP eram
14 chamadas a `asset()`. O texto dos três epílogos é cânone (`docs/codex/`) e foi para
`config/finais.php`, não para dentro de uma view — uma mudança ali deve aparecer no diff
como decisão narrativa, e não como ajuste de layout.

Verificado por HTTP contra o servidor e os dados importados: as dez telas respondem 200,
as quatro escritas testadas por GET respondem 405, e a tentativa de vender o Fragmento
deixou ouro e inventário intactos.

### Varredura de completude

Uma auditoria comparando o legado com o port achou o que ainda faltava, e tudo foi
portado:

- **Os quatro serviços derivados**, que o legado nunca testou: maestria por matéria,
  missões da semana, domínio das regiões e "Primeiros passos". Todos são lógica pura,
  read-only, sem cron e sem tabela nova. Agora têm 34 testes.
- **O splash** (`HomeController`). O port tinha feito `/` ser o mapa; agora `/` é a
  vitrine para o visitante e um atalho para quem já joga.
- **O bestiário** (23 entradas) e a **leitura do inimigo** — a lore na arena, e o chip
  de tática no mapa que diz o que levar. Vazio para inimigos comuns, de propósito: um
  aviso em toda fase não avisa nada.
- **O Poder Total** na loja, com a comparação ciente de slot. O número absoluto do item
  não diz se vale trocar; o que importa é o Poder que o herói terá depois.
- Os scripts `som.js`, `ui.js`, `app.js` e `celebracao.js`, e a página 404.

Regras de apresentação que o port precisou preservar, e que hoje têm teste:

- A barra de maestria mede o **fator mais atrasado** entre acumular acertos e sustentar
  precisão. Se medisse só os acertos, encheria enquanto a precisão despencava — uma
  barra que mente.
- A missão de precisão mede **volume** até o mínimo e só então precisão. Uma barra de
  precisão cheia com duas respostas não mediria nada.
- "Região dominada" exige 3 estrelas em cada fase principal — as secundárias, sendo
  opcionais, não podem impedir o domínio.
- O primeiro item dos "Primeiros passos" **já nasce concluído** (*endowed progress*), e
  o painel some quando o herói cresce.
- Uma conquista secreta **não revela o nome** antes de ser obtida: spoiler é o oposto de
  recompensa.

Duas traduções de SQL que não existiam no PostgreSQL: `YEARWEEK(x, 3)` virou
`date_trunc('week', x)` (que também começa na segunda-feira) e `SUM(pf.estrelas = 3)`
virou `COUNT(*) FILTER (WHERE …)`, já que o PostgreSQL não soma booleanos.

**Auditoria final:** os 9 serviços do legado têm par no port; os 45 métodos públicos dos
10 controllers estão cobertos por 50 rotas.

**Ainda de fora:** a arte (143 MB) está por symlink em `platform/public/img`; o deploy a
copia (Fase 5).

**Critério de aceite:** atingido — 196 testes verdes no port, 38 no legado, PHPStan
nível 6 limpo.

### Fase 5 — Corte ✅ **ARTEFATOS PRONTOS E EXERCITADOS** · ⏸ **corte não executado**

Tudo o que o corte precisa existe e foi rodado de ponta a ponta **localmente**.
Nada foi feito na VPS: falta credencial, e um deploy de produção não se faz sem
alguém olhando. Runbook completo em [`docs/operacao/RUNBOOK.md`](../operacao/RUNBOOK.md).

- **Imagem** (186 MB): php-fpm 8.4 alpine, `pdo_pgsql`, OPcache com
  `validate_timestamps=0`. O `.dockerignore` derruba o contexto de build de 2,5 GB
  para 1 KB. A arte (143 MB) fica fora da imagem, em bind-mount de leitura: assá-la
  tornaria deploy e rollback caros para guardar arquivos que quase nunca mudam.
- **O entrypoint recusa subir** com `APP_KEY` vazio ou `APP_DEBUG=true` em produção.
  O `config:cache` do Laravel não valida nenhum dos dois — o primeiro só quebraria
  no primeiro login, e o segundo entregaria um stack trace a qualquer aluno.
- **nginx** serve a arte como estático imutável, bloqueia PHP fora do front
  controller e nega dotfiles. Verificado: `/.env` responde 403.
- **`bin/deploy.sh`** recusa árvore suja, faz dump, constrói, migra, sobe, espera o
  health check e roda o smoke. Se o smoke reprovar, volta sozinho para a tag
  anterior. A tag é o SHA do commit, então rollback não precisa de rebuild.
- **`bin/restore.sh --ensaio`** restaura num banco descartável e confere as
  contagens. Um backup nunca restaurado não é um backup; é uma esperança.
- **`algorithmia:smoke`** joga uma fase real dentro de uma transação e a desfaz.
  Checa o que um health check não checa: conteúdo importado, IDs das fases
  secundárias preservados, Fragmento no catálogo, e que o gabarito não vaza.

O smoke se pagou antes de existir produção: rodando pela primeira vez no banco de
desenvolvimento, ele denunciou que a migration `recompensas_batalha` nunca fora
aplicada ali. Uma deriva de schema que teria ido para o corte em silêncio.

**Verificado localmente, no stack de produção real:** migrations, importação de
1.306 linhas de dentro do container, login com a senha original, prólogo, batalha
atrás do nginx, gabarito não vazando, `GET` de escrita em 405, backup, ensaio de
restauração, `deploy.sh` completo e `rollback.sh` completo.

**Falta para o corte de verdade** (§8 e §9 do runbook): credenciais e acesso à VPS,
`.env.producao`, pôr o legado em somente leitura, apontar o DNS, e a janela de
coexistência.

**Host confirmado (2026-07-09):** VPS com root, rodando Docker. É um servidor
RHEL/AlmaLinux estilo cPanel — mas com root, e por isso o Docker roda ali. O `httpd`
do legado e os containers do port convivem durante o corte.

**A armadilha da coexistência:** o `httpd` já é dono da porta 80. O nginx do port sobe
numa porta alta e o `httpd` faz proxy reverso. Atrás desse proxy, o Laravel precisa de
`TRUSTED_PROXIES` para saber que a conexão é HTTPS — sem isso, o cookie `secure` nunca
é enviado e o jogador cai na tela de login para sempre. Ver
[`RUNBOOK.md §9`](../operacao/RUNBOOK.md).

**Critério de aceite:** rollback exercitado — ver abaixo.

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

## 7. A tentativa anterior (Codex)

Antes deste plano, um agente Codex executou o roteiro v1.0 e empurrou 73 commits
para a `main`: uma fundação Laravel em `platform/` com tenancy, RLS, resolução de
tenant por host e autenticação por tenant — 15.898 linhas, 127 arquivos.

Ela foi descartada, e a `main` reescrita a partir de `1be0c42`. O motivo não é
qualidade de código: é que ela construiu exatamente as duas coisas que a decisão
de §1 removeu do escopo, e nunca tocou no que importa. `app/Domain/Game`,
`Content` e `Progress` eram apenas arquivos `README.md` vazios. O motor de batalha
não foi portado, e não existia um único teste sobre o legado — a Fase 0 do roteiro
v1.0 entregava documentação, não rede de segurança. É o desalinhamento descrito
em §2.

**Nada foi perdido.** O trabalho está preservado em dois refs no GitHub:
`codex/fundacao-multitenant` e `validate-etapa-2-0e`, ambos em `44fe948`. Quando a
multitenancy voltar (§6), vale reler o `ResolveTenantFromHost` e o
`DatabaseRoleProvisioner` de lá antes de escrever do zero.

---

## 8. Como rodar os testes

### Vetores-ouro do legado (MySQL)

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

### Port em Laravel (PostgreSQL)

```bash
cd platform
docker compose up -d          # PostgreSQL 18 (55432) + banco de teste (55433) + Mailpit
composer install
cp .env.example .env && php artisan key:generate
php artisan migrate

php artisan test              # roda contra o PostgreSQL da porta 55433
vendor/bin/pint --test
vendor/bin/phpstan analyse --memory-limit=1G
```
