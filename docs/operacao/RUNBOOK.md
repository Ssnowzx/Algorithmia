# Runbook — Algorithmia (port em Laravel)

Como operar o jogo em produção. Escrito para ser lido às 3 da manhã, por alguém
com sono, quando algo já deu errado.

---

## 0. Antes de qualquer coisa

**Uma contradição a resolver.** O [`AGENTS.md`](../../AGENTS.md) descreve a produção
atual como host cPanel/RHEL com `httpd` e MySQL em `/home/algorithmia/public_html`. O
port pressupõe uma VPS com Docker. As duas coisas não podem ser verdade ao mesmo tempo.

Este runbook cobre o **caminho Docker**, que é o único verificado de ponta a ponta.
O §9 esboça a alternativa nativa com `httpd`, e ela **não foi testada**.

---

## 1. O que está no ar

```
nginx (web)  ──► php-fpm (app)  ──►  PostgreSQL 18 (postgres)
   :80              :9000                   :5432
    │                  │
    │                  └─ volume `storage` (logs, cache de arquivo)
    └─ bind-mount read-only de `public/img` (143 MB de arte)
```

A imagem é etiquetada com o **SHA do commit**. Não existe `git pull` no servidor: o
que roda é exatamente o que foi construído.

Arquivos que só existem no servidor, nunca no git:

| Arquivo | Contém |
|---|---|
| `platform/.env.producao` | `APP_KEY`, senha do banco, credenciais do legado |
| `platform/.env.producao.postgres` | `POSTGRES_*` |
| `platform/.deploy/tag-atual` e `tag-anterior` | estado do deploy |
| `backups/*.sql.gz` | dumps — **contêm dados de alunos** |

---

## 2. Primeiro deploy

```bash
git clone https://github.com/Ssnowzx/Algorithmia.git /srv/algorithmia
cd /srv/algorithmia

cp platform/.env.producao.exemplo platform/.env.producao
cat > platform/.env.producao.postgres <<'EOF'
POSTGRES_DB=algorithmia
POSTGRES_USER=algorithmia
POSTGRES_PASSWORD=<senha forte>
EOF

# A APP_KEY não pode faltar: o entrypoint recusa subir sem ela, de propósito.
docker run --rm -e ALGORITHMIA_PULAR_OTIMIZACAO=1 algorithmia:latest \
    php artisan key:generate --show
# cole em APP_KEY e repita a senha em DB_PASSWORD

bin/deploy.sh
```

O `deploy.sh` faz, nesta ordem: recusa árvore suja → dump do banco → build da imagem
→ migrations → sobe os containers → espera o health check → **smoke**. Se o smoke
reprovar, ele volta sozinho para a tag anterior.

### Importar os dados do jogo antigo (uma vez só, no dia do corte)

```bash
cd platform
export ALGORITHMIA_TAG="$(cat .deploy/tag-atual)"

# `ALGORITHMIA_PULAR_OTIMIZACAO=1`: o container é efêmero e o cache de config já
# existe na instância que está no ar. Regerá-lo aqui só gastaria tempo.
docker compose -f compose.prod.yml run --rm --no-deps \
    -e ALGORITHMIA_PULAR_OTIMIZACAO=1 \
    app php artisan algorithmia:importar --dry-run   # ensaia tudo e desfaz

docker compose -f compose.prod.yml run --rm --no-deps \
    -e ALGORITHMIA_PULAR_OTIMIZACAO=1 \
    app php artisan algorithmia:importar             # para valer
```

As credenciais do MySQL vêm de `LEGADO_DB_*` no `.env.producao`. A importação
**nunca escreve no legado**: o jogo antigo precisa seguir de pé.

O `--dry-run` roda a importação inteira dentro de uma transação e a desfaz. Ele
exercita chaves estrangeiras e conversão de tipos. Um ensaio que não faz isso não
prova nada.

---

## 3. Deploy do dia a dia

```bash
git pull --ff-only && bin/deploy.sh
```

### Migrations: a regra que não se negocia

Elas rodam **antes** de o código novo subir, contra o código velho ainda no ar. Isso
só é seguro se cada migration for **aditiva**: criar tabela, criar coluna anulável,
criar índice.

Remover ou renomear coluna quebra o código velho no intervalo — e o rollback **não
desfaz migration**. Se precisar remover algo, faça em dois deploys: primeiro pare de
usar a coluna, depois remova.

---

## 4. Rollback

```bash
bin/rollback.sh                                  # só o código
bin/rollback.sh --com-banco backups/pre-XXXX.sql.gz   # código + banco
```

**O que o rollback de código faz:** aponta a tag da imagem para a versão anterior e
sobe de novo. Segundos, sem rebuild.

**O que ele NÃO faz:** desfazer migrations. Se a migration do deploy ruim era aditiva,
o código antigo simplesmente a ignora e tudo volta ao normal. Se ela removeu algo, o
código antigo quebra, e aí só resta restaurar o dump — **perdendo o que os jogadores
fizeram desde então**.

É por isso que a regra do §3 existe.

---

## 5. Backup e restauração

```bash
bin/backup.sh                                    # dump comprimido e verificado
bin/restore.sh --ensaio backups/xxx.sql.gz       # restaura num banco descartável
bin/restore.sh backups/xxx.sql.gz                # DESTRUTIVO, pede confirmação
```

O `backup.sh` roda automaticamente antes de cada deploy, verifica o gzip e recusa um
dump suspeito de tão pequeno. Mantém os 30 mais recentes.

**Um backup nunca restaurado não é um backup; é uma esperança.** Rode
`bin/restore.sh --ensaio` no dump mais recente pelo menos uma vez por mês. Leva
segundos e não toca na produção.

Sugestão de cron:

```cron
15 4 * * *  cd /srv/algorithmia && bin/backup.sh >> /var/log/algorithmia-backup.log 2>&1
30 4 * * 0  cd /srv/algorithmia && bin/restore.sh --ensaio "$(ls -t backups/*.sql.gz | head -1)" >> /var/log/algorithmia-backup.log 2>&1
```

---

## 6. O que este runbook já viu quebrar

Os scripts foram exercitados de ponta a ponta antes de existir produção, inclusive
um deploy propositalmente ruim. O ensaio expôs cinco defeitos reais, todos
corrigidos. Ficam registrados porque são o tipo de coisa que volta:

1. **O gate de saúde do deploy fazia `grep -q healthy`** — e `unhealthy` casa com
   `healthy`. O portão declarava sucesso instantaneamente, sempre.
2. **O healthcheck do nginx usava `localhost`.** O `wget` do BusyBox resolve
   `localhost` para `::1` antes de `127.0.0.1`, e o nginx só escuta em IPv4. O
   container ficou `unhealthy` o tempo todo, e ninguém percebeu por causa de (1).
3. **`set -o pipefail` + `| grep -q`**: o `grep` fecha o cano ao casar, o produtor
   morre de SIGPIPE (141) e o `pipefail` propaga esse 141. A condição jamais era
   verdadeira. Toda espera de prontidão abortaria após 90 s.
4. **O rollback automático revertia e saía sem esperar.** O entrypoint leva alguns
   segundos gerando os caches; nesse intervalo o nginx devolve 502, e o operador ia
   dormir achando que estava tudo bem.
5. **`bin/backup.sh` não rodava sozinho.** O `compose` interpola a imagem do serviço
   `app` mesmo quando o comando só toca o postgres, e recusa o arquivo sem
   `ALGORITHMIA_TAG`. O backup do cron falharia todas as noites.

E o `algorithmia:smoke` se pagou antes de existir produção: na primeira execução, no
banco de desenvolvimento, denunciou que a migration `recompensas_batalha` nunca fora
aplicada ali.

---

## 7. Quando algo quebra

### O site não responde

```bash
cd platform
docker compose -f compose.prod.yml ps
docker compose -f compose.prod.yml logs --tail=100 app web
docker compose -f compose.prod.yml exec web wget -qO- http://127.0.0.1/healthz
```

`healthz` devolve **503** quando o banco não responde. Um balanceador que só olhe
para o `/up` do Laravel mandaria tráfego a uma instância sem banco.

Use `127.0.0.1`, nunca `localhost`: o `wget` do BusyBox tenta `::1` primeiro, e o
nginx só escuta em IPv4. Ver §6.

### O jogo responde, mas está errado

```bash
export ALGORITHMIA_TAG="$(cat platform/.deploy/tag-atual)"
docker compose -f platform/compose.prod.yml exec app php artisan algorithmia:smoke
```

O smoke joga uma fase real dentro de uma transação e a desfaz. Ele checa o que um
health check não checa: conteúdo importado, IDs das fases secundárias preservados,
Fragmento da IA no catálogo, e que o gabarito não vaza para o cliente.

### Sintomas e causas

| Sintoma | Provável causa |
|---|---|
| `APP_KEY está vazio` no boot | `.env.producao` sem chave — o entrypoint recusa de propósito |
| `APP_DEBUG=true com APP_ENV=production` | idem; um stack trace entrega caminhos de arquivo a quem provocar um erro |
| Smoke: "mestres está vazia" | faltou `algorithmia:importar` |
| Smoke: "IDs das fases secundárias" | a importação não preservou IDs — a conquista `arquivista_do_vazio` ficaria inalcançável **em silêncio** |
| Imagens 404 | o bind-mount de `public/img` não subiu; confira o caminho no `compose.prod.yml` |
| Código antigo servindo após deploy | não acontece: `opcache.validate_timestamps=0` numa imagem imutável. Se acontecer, alguém montou código por volume |

---

## 8. Coexistência e corte

Durante a janela de corte, os dois sistemas ficam de pé:

1. **Antes:** dump do MySQL legado (`mysqldump`) e do PostgreSQL.
2. Ponha o legado em **somente leitura** (revogue INSERT/UPDATE/DELETE do usuário da
   aplicação). Assim ninguém joga no sistema velho enquanto os dados migram.
3. `algorithmia:importar --dry-run`, depois sem a flag.
4. `bin/deploy.sh` e `algorithmia:smoke`.
5. Aponte o DNS/vhost de `algorithmia.tars.art.br` para o nginx do port.
6. Deixe o legado de pé, em leitura, por uma janela combinada. **Ele é o plano de
   rollback de verdade** enquanto o port não tiver rodado alguns dias.

Rollback do corte: aponte o DNS de volta e devolva a escrita ao legado. Os dados que
os jogadores criaram no port nesse intervalo **não voltam** — por isso a janela deve
ser curta e anunciada.

---

## 9. Alternativa não verificada: host nativo com `httpd`

Se a produção continuar no host cPanel do `AGENTS.md`, o Docker não entra. O caminho
seria:

- PHP 8.3+ com `pdo_pgsql`, PostgreSQL 18 instalado no host.
- Document root apontando para `platform/public` (hoje aponta para a raiz do legado).
- `composer install --no-dev`, `php artisan migrate --force`, `php artisan optimize`.
- **Reiniciar o `httpd` após cada deploy**, senão o OPcache serve o código antigo.
- Copiar (não linkar) `public/img` para `platform/public/img`.

**Isto não foi testado.** Se for o caminho, ele precisa de um ensaio completo — deploy,
smoke e rollback — antes de tocar em produção.
