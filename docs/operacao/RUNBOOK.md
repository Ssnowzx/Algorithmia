# Runbook — Algorithmia (port em Laravel)

Como operar o jogo em produção. Escrito para ser lido às 3 da manhã, por alguém
com sono, quando algo já deu errado.

---

## 0. O host

### O que pedir, numa VPS nova

| | Mínimo | Por quê |
|---|---|---|
| Acesso | **root** | Docker, firewall, portas baixas |
| RAM | **2 GiB** (4 GiB confortável) | o `docker build` do PHP chega perto de 1 GiB de pico; postgres + php-fpm + nginx querem ~600 MiB em repouso |
| Disco | **20 GiB** | imagem, volumes, `backups/`, os 143 MB de arte |
| SO | Debian/Ubuntu ou RHEL/AlmaLinux | qualquer um com Docker Engine e `compose >= 2.1.1` |
| Rede | uma porta alta livre | o nginx do port publica nela (`ALGORITHMIA_PORTA`, padrão `8080`) |

**Prefira um host dedicado.** Num host compartilhado — Virtualmin, cPanel com vários
sites, qualquer máquina com containers de outros donos — três coisas mudam, e todas já
nos morderam:

- `systemctl restart httpd` derruba **todos** os sites, não só o seu. Use `reload`.
- A faixa `172.x` do Docker alcança os containers de **todos** os tenants. Um
  `GRANT ... TO 'x'@'172.%'` entrega o banco dos alunos a qualquer um deles. Por isso a
  importação lê o legado por **socket Unix** (§2).
- Fechar uma porta no firewall pode quebrar o site de outro dono.

### Antes de marcar 6.10/6.11

Rode no host:

```bash
bash bin/checar-host.sh     # somente-leitura: não instala, não altera, não escreve
```

Sem **root + Docker + `compose >= 2.1.1` + `mod_proxy_http` + porta alta livre + 1 GiB
de RAM disponível**, o corte descrito neste runbook não roda como está escrito. Onde o
script não puder saber, ele diz que não sabe — não trate silêncio como aprovação.

Se faltar só a RAM, ainda dá: construa a imagem noutra máquina e traga-a pronta, com
`docker save algorithmia:TAG | ssh o-host 'docker load'`. Se faltar Docker ou root, o
port precisa de outro plano de execução (PHP-FPM sob o painel), e o §8 tem de ser
reescrito.

### Durante o corte

**O `httpd` do legado é dono da porta 80.** O nginx do port publica numa porta alta, e o
`httpd` faz proxy reverso para ela — assim o certificado TLS existente segue valendo.
Ver §9. Aposentado o legado, o nginx assume a 80/443 e `TRUSTED_PROXIES` volta a vazio.

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
| `platform/.deploy/ambiente` | portas, conf do nginx, caminho dos certificados (§10) |
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

# `--sem-conteudo` SÓ aqui: o banco ainda está vazio. A importação roda logo abaixo,
# e ela precisa do container `app` de pé — daí a ordem. Sem a flag, o smoke reprova
# as checagens de conteúdo e o deploy aborta.
bin/deploy.sh --sem-conteudo
```

O `deploy.sh` faz, nesta ordem: recusa árvore suja → dump do banco → build da imagem
→ migrations → sobe os containers → espera o health check → **smoke**. Se o smoke
reprovar, ele volta sozinho para a tag anterior.

Depois de importar (abaixo), rode `bin/deploy.sh` **sem** a flag pelo menos uma vez,
ou `algorithmia:smoke` direto: é o smoke completo que prova que o jogo está jogável.
Do segundo deploy em diante, nunca mais use `--sem-conteudo` — ela existe para o
banco vazio, e um deploy que perdeu o conteúdo tem de reprovar.

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

#### Alcançar o MySQL do host a partir do container — pelo socket, não pela rede

O legado roda no host; o `importar` roda dentro do container `app`. **Monte o socket
do MySQL no container.** Assim a importação não precisa de porta TCP aberta nenhuma:

```bash
docker compose -f compose.prod.yml run --rm --no-deps \
    -e ALGORITHMIA_PULAR_OTIMIZACAO=1 \
    -e LEGADO_DB_SOCKET=/var/run/mysqld/mysqld.sock \
    -v /var/lib/mysql/mysql.sock:/var/run/mysqld/mysqld.sock \
    app php artisan algorithmia:importar --dry-run
```

```sql
-- Conexão por socket casa com @'localhost'. Apenas SELECT: é o que torna o passo 3
-- do §8 uma garantia, e não uma promessa.
CREATE USER 'algorithmia_ro'@'localhost' IDENTIFIED BY '<senha>';
GRANT SELECT ON algorithmia.* TO 'algorithmia_ro'@'localhost';
```

Confirme o caminho do socket antes: `mysqladmin variables | grep '^| socket'`. Em
RHEL/AlmaLinux costuma ser `/var/lib/mysql/mysql.sock`; em Debian,
`/run/mysqld/mysqld.sock`.

> ⚠️ **Não abra o MySQL em TCP para `172.x` só para importar.** Numa VPS dedicada
> seria apenas feio. Num host **compartilhado** — Virtualmin, cPanel com vários sites,
> qualquer máquina com containers de outros donos — a faixa `172.x` alcança os
> containers de **todos** os tenants, e `GRANT ... TO 'x'@'172.%'` entrega o banco dos
> alunos a qualquer um deles. Se você já tem `bind-address = 0.0.0.0` e a 3306 aberta
> no firewall, isso é um incidente aberto agora, e não uma questão de deploy.

Se, e só se, o host for **dedicado** e você não puder usar o socket, existe a rota TCP:
`LEGADO_DB_HOST=host.docker.internal` (o `compose.prod.yml` mapeia o nome para o
gateway via `extra_hosts`), com o MySQL escutando naquela interface e o `GRANT`
restrito à sub-rede exata. É a segunda opção, não a primeira.

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
um deploy propositalmente ruim e um ensaio completo do corte (§8) contra um MySQL
legado de mentira. Os ensaios expuseram oito defeitos reais, todos corrigidos.
Ficam registrados porque são o tipo de coisa que volta:

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

Os três seguintes só apareceram no ensaio do corte, porque **todos os ensaios
anteriores rodaram sobre um volume de banco já populado.** Eram, os três, defeitos
exclusivos do primeiro deploy — o único que a VPS ainda vai rodar:

6. **`deploy.sh` esperava o postgres iniciar, não ficar pronto.** `up -d postgres`
   volta quando o container arranca; num volume vazio ele ainda roda `initdb` antes
   de escutar. O `migrate` da linha seguinte usa `--no-deps`, que manda o compose
   ignorar o `depends_on: service_healthy`. Morria em `SQLSTATE[08006] connection
   refused`. Corrigido com `up -d --wait`.
7. **O primeiro deploy não tinha como passar no smoke.** O smoke exige conteúdo
   importado; a importação só roda depois, e precisa do container `app` de pé. O
   `Smoke.php` já tinha `--sem-conteudo`, mas o `deploy.sh` nunca a repassava. O §2 e
   o §8 deste runbook descreviam, portanto, uma sequência impossível.
8. **O §9 prescrevia `TRUSTED_PROXIES=127.0.0.1`** — e avisava, doze linhas abaixo,
   que o request chega pelo gateway do Docker. Quem copiasse o trecho veria o jogador
   logar e cair num `400 Bad Request`: sem confiar no proxy, o Laravel gera
   `Location: http://…` apontando para a porta TLS. Medido no ensaio: `192.168.65.1`.

E um nono, que o ensaio do §10 pegou antes de existir a VPS nova:

9. **O rollback teria desfeito o TLS, em silêncio.** `ALGORITHMIA_NGINX_CONF` e as
   portas viviam só no ambiente do operador. Um `rollback.sh` no dia seguinte subiria o
   `web` com os padrões do compose — `algorithmia.conf`, HTTP na 8080 — e o site
   responderia `200` em texto claro, sem nada acusar. Por isso `deploy.sh` e
   `rollback.sh` leem `platform/.deploy/ambiente` (§10.3). Verificado nos dois sentidos:
   com o arquivo, o rollback preserva a 443; sem ele, o compose volta a `algorithmia.conf`.

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

### "Sumiu uma fase" · "alguém apagou meus desafios"

Toda ação do Painel do Mestre deixa registro. A trilha guarda **o que havia antes** —
numa exclusão, é a única cópia que sobra.

```bash
export ALGORITHMIA_TAG="$(cat platform/.deploy/tag-atual)"
docker compose -f platform/compose.prod.yml exec postgres \
  psql -U algorithmia -d algorithmia -c "
    SELECT created_at, autor_email, acao, alvo_id, resumo, request_id
      FROM auditoria
     WHERE acao LIKE '%.excluir'
     ORDER BY created_at DESC
     LIMIT 20;"
```

O `resumo` de um `fase.excluir` traz o nome da fase e **quantos desafios foram na
cascata**. O de um `item.excluir` traz quantos inventários perderam o item.

A trilha não tem chave estrangeira para o alvo nem para o autor, de propósito: apagar
a fase, ou a conta de quem a apagou, não pode apagar o registro de que isso aconteceu.
E não há `updated_at` — uma linha de auditoria que pode ser editada não é auditoria.

### Ligar um relato a uma linha de log

Toda resposta traz `X-Request-Id`. O mesmo valor aparece no `context` de cada linha de
log daquela requisição e na coluna `request_id` da auditoria.

```bash
# o jogador copia o id da aba de rede do navegador, ou você o tira da auditoria
docker compose -f platform/compose.prod.yml logs app | grep '<o-uuid>'
```

Os logs saem em JSON, uma linha por evento, com `request_id`, `usuario_id`, `ip` e
`rota` no contexto (`LOG_STDERR_FORMATTER` no `.env.producao`). Sem isso, investigar é
ler as linhas de trinta jogadores simultâneos intercaladas, sem nada que as separe.

O identificador **nunca vem do cliente**: aceitá-lo de um cabeçalho deixaria qualquer
um forjar a trilha, ou colidir com a de outro.

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
| Login em laço: o jogador entra e volta à tela de login | `SESSION_SECURE_COOKIE=true` sem `TRUSTED_PROXIES` atrás do `httpd`. O Laravel acha que a conexão é `http` e não envia o cookie. Ver §9 |
| Links e redirecionamentos saem em `http://` | idem |

---

## 8. O corte, passo a passo

Os dois sistemas ficam de pé na mesma VPS. A rede é o §9 — **leia antes**.

> **Pré-requisito, e não é formalidade:** o host precisa ter root, Docker com
> `compose >= 2.1.1`, e `mod_proxy_http` no `httpd`. Num cPanel de verdade nada disso é
> garantido, e vhost editado à mão é sobrescrito no próximo rebuild. **Verifique antes**
> — não confie neste documento sobre o assunto, ele não roda comandos na sua máquina.

0. **O legado em produção precisa estar em dia.** O importador faz `SELECT *`: ele copia
   o estado que encontrar. Se a prod não tem as migrations do legado aplicadas, você
   migra os bugs junto — `mestres` e `itens` duplicados, as conquistas de objetivo da
   loja ausentes. E o `migrate.php` **escreve**, então ele tem de rodar **antes** do
   passo 3, que tranca o banco.

   ```bash
   # na VPS, no diretório do legado, como o usuário do site
   git pull
   DB_HOST=… DB_NAME=… DB_USER=… DB_PASS=… php database/migrate.php   # idempotente
   mysql -u … -e 'SELECT arquivo FROM migracoes_aplicadas ORDER BY 1;'   # espere 6
   ```

1. **Antes de tudo:** dump do MySQL legado (`mysqldump`) e do PostgreSQL.
2. Suba o port em porta alta e **verifique-o pela porta**, sem tocar no domínio:
   `bin/deploy.sh --sem-conteudo`. O banco ainda está vazio, e o smoke completo só
   passa depois do passo 4 — por isso a flag. Confira `curl http://127.0.0.1:8080/healthz`.
   O legado segue atendendo os jogadores.
3. Ponha o legado em **somente leitura** (revogue INSERT/UPDATE/DELETE do usuário da
   aplicação). Ninguém deve jogar no sistema velho enquanto os dados migram.
4. `algorithmia:importar --dry-run`, depois sem a flag. Confira a reconciliação.
5. Rode `algorithmia:smoke` de novo — agora **sem** `--sem-conteudo`, com o conteúdo
   real. É esta execução que exercita o motor numa fase de verdade.
6. **Só então** troque o vhost do `httpd` para o proxy reverso do §9, e configure
   `TRUSTED_PROXIES`. Recarregue o `httpd`.
7. Entre no jogo você mesmo: login, mapa, uma batalha. O smoke não testa a sessão
   atrás do proxy — o §9 explica por que ela é o ponto frágil.
8. Deixe o legado de pé, em leitura, por uma janela combinada. **Ele é o plano de
   rollback de verdade** enquanto o port não tiver rodado alguns dias.

**Rollback do corte:** devolva o vhost ao legado e restaure a escrita. Os dados que os
jogadores criaram no port nesse intervalo **não voltam** — por isso a janela deve ser
curta e anunciada.

---

## 9. Topologia de rede durante o corte

Os dois servidores não podem escutar a porta 80 ao mesmo tempo. Durante a janela de
corte, o `httpd` continua dono do domínio e da TLS, e repassa ao port.

### No `httpd`

```apache
# vhost de algorithmia.tars.art.br, depois do corte
ProxyPreserveHost On
RequestHeader set X-Forwarded-Proto "https"
ProxyPass        / http://127.0.0.1:8080/
ProxyPassReverse / http://127.0.0.1:8080/
```

### No `.env.producao`

```dotenv
# NÃO copie um IP daqui. Meça o seu — veja "Descobrir o IP do proxy" abaixo.
# O `httpd` roda no host e alcança o nginx pela porta publicada: o request chega
# ao container pelo NAT do Docker, com o endereço do GATEWAY da rede do compose
# (172.x.0.1), e não por 127.0.0.1. Ensaiado: num Docker Desktop o valor era
# 192.168.65.1. Se você puser 127.0.0.1, o proxy não é confiado, o Laravel acha
# que a conexão é http, e o jogador loga e cai em Bad Request.
TRUSTED_PROXIES=<o endereço que você mediu>
SESSION_SECURE_COOKIE=true
APP_URL=https://algorithmia.tars.art.br
```

**Isto não é opcional.** Sem `TRUSTED_PROXIES`, o Laravel não sabe que a conexão é
HTTPS: gera URLs `http://`, o cookie `secure` nunca é enviado de volta, e o jogador
loga e cai na tela de login de novo — para sempre. É o tipo de bug que só aparece em
produção, porque em desenvolvimento não há proxy nem TLS.

O oposto também morde: **não use `TRUSTED_PROXIES=*`** se o nginx atender direto.
Qualquer cliente poderia forjar `X-Forwarded-Proto` e `X-Forwarded-Host`. A variável
é vazia por padrão, e o comportamento está travado em
`platform/tests/Feature/ProxyReversoTest.php`.

> O nginx do port **não** repassa `HTTPS` ao PHP a partir de `X-Forwarded-Proto`. O
> Symfony considera segura qualquer variável `HTTPS` não-vazia e diferente de `"off"`
> — o valor literal `"http"` seria lido como HTTPS. Quem decide o esquema é o Laravel,
> pelo `trustProxies`.

### Depois do corte

Aposentado o legado, o nginx pode assumir a 80/443 diretamente (com a TLS migrada), e
`TRUSTED_PROXIES` volta a ficar **vazio**.

### Descobrir o IP do proxy

Faça **uma** requisição pelo domínio (ou pela porta alta, pelo `httpd`) e leia o
`$remote_addr` que o nginx registrou. É a primeira coluna do log de acesso.

```bash
cd platform
curl -sk https://algorithmia.tars.art.br/ -o /dev/null   # gera uma linha de log
docker compose -f compose.prod.yml logs web | tail -5    # o $remote_addr é a 1ª coluna
```

Ignore as linhas de `/healthz` com `User-Agent: Wget`: são o healthcheck interno do
container, e ele **sempre** vem de `127.0.0.1`. É essa coincidência que faz alguém
concluir, olhando o log rápido demais, que o proxy é o loopback.

### Verificar que o `TRUSTED_PROXIES` pegou

Um `curl` prova em dois segundos o que só apareceria com o jogo no ar:

```bash
# O Location DEVE começar com https://. Se vier http://, o proxy não está confiado.
curl -sk -o /dev/null -D - -X POST https://algorithmia.tars.art.br/entrar \
     -d '_token=...' -d 'email=...' -d 'password=...' | grep -i '^location:'
```

Num Docker padrão no Linux, requisições vindas do `httpd` do host chegam pelo gateway
da rede do compose, não por `127.0.0.1`. **Confira antes de assumir.**

---

## 10. Corte para uma VPS nova (o legado fica onde está)

Este é o caminho quando o port sobe numa **máquina dedicada** e o legado continua no
host antigo. É mais seguro que o §8: não há dois servidores disputando a porta 80, o
`TRUSTED_PROXIES` fica **vazio** (não há proxy em quem confiar), e o rollback é uma
troca de DNS — o legado nunca sai do ar.

O §8 continua valendo para o caso em que os dois convivem na mesma máquina.

### 10.1 Provisionar

Requisitos no §0. Instale Docker Engine + `compose >= 2.1.1` e `git`. Clone o repo.

```bash
git clone https://github.com/Ssnowzx/Algorithmia.git /srv/algorithmia
cd /srv/algorithmia
cp platform/.env.producao.exemplo platform/.env.producao
```

No `.env.producao`:

```dotenv
APP_URL=https://algorithmia.exemplo.com
TRUSTED_PROXIES=            # VAZIO. O nginx termina o TLS; não há proxy na frente.
SESSION_SECURE_COOKIE=true
TENANCY_ATIVA=true
```

> ⚠️ **O `APP_URL` precisa estar certo ANTES do primeiro `bin/deploy.sh`.**
>
> A migration cria a instituição padrão com o host tirado dele, e é esse host que o
> `ResolverTenant` procura em `tenant_dominios` a cada requisição. Um `APP_URL` errado
> produz o pior sintoma possível: **o site responde 404 com o banco cheio e o healthcheck
> verde**. O `/healthz` fica fora do resolvedor de propósito — ele existe para dizer se o
> banco está de pé, não de quem são os dados.
>
> Conserto, se já aconteceu:
> `UPDATE tenant_dominios SET host = 'o.dominio.certo' WHERE tenant_id = 1;`

### 10.2 O certificado, antes do primeiro deploy

A porta 80 ainda está livre — é a única janela em que o `--standalone` funciona.

```bash
certbot certonly --standalone -d algorithmia.exemplo.com
```

Renovação depois, sem parar o site (o nginx serve o desafio ACME em texto claro na
80, e só ela — todo o resto é 301 para HTTPS):

```bash
certbot renew --webroot -w /srv/algorithmia/platform/docker/nginx/certbot \
  --deploy-hook 'docker exec algorithmia-web-1 nginx -s reload'
```

### 10.3 A topologia, num arquivo

`bin/deploy.sh` e `bin/rollback.sh` leem `platform/.deploy/ambiente`. **Escreva-o antes
do primeiro deploy.** Sem ele, um rollback futuro devolve o site a HTTP na 8080 — e não
avisa, porque esses são os padrões do compose.

```bash
mkdir -p platform/.deploy
cat > platform/.deploy/ambiente <<'EOF'
export ALGORITHMIA_PORTA=80
export ALGORITHMIA_PORTA_TLS=443
export ALGORITHMIA_NGINX_CONF=algorithmia-tls.conf
export ALGORITHMIA_CERTS=/etc/letsencrypt/live/algorithmia.exemplo.com
EOF
```

### 10.4 Subir o port, ainda sem conteúdo

```bash
bin/deploy.sh --sem-conteudo
```

O banco está vazio; a flag existe para isso, e **só para o primeiro deploy** (§2).
Confira pelo IP, sem depender do DNS:

```bash
curl -k --resolve algorithmia.exemplo.com:443:127.0.0.1 https://algorithmia.exemplo.com/healthz
```

### 10.5 Trazer os dados — o legado nunca é exposto na rede

O legado está noutra máquina. **Não abra a 3306 dele para a internet.** Traga um dump,
restaure-o num MySQL efêmero na rede do compose, importe dali, e derrube-o.

```bash
# na máquina do legado — depois de pô-lo em somente-leitura (§8, passo 3)
mysqldump -u USUARIO -p --single-transaction --set-gtid-purged=OFF algorithmia \
  | gzip > legado.sql.gz
scp legado.sql.gz nova-vps:/srv/algorithmia/

# na VPS nova
gunzip -c /srv/algorithmia/legado.sql.gz > /tmp/legado.sql
docker run -d --name legado --network algorithmia_default \
    -e MYSQL_ROOT_PASSWORD="$(openssl rand -hex 16)" \
    -e MYSQL_DATABASE=algorithmia mysql:9
# espere o `mysqladmin ping` responder, então carregue o dump e crie o leitor:
#   CREATE USER 'algorithmia_ro'@'%' IDENTIFIED BY '<senha>';
#   GRANT SELECT ON algorithmia.* TO 'algorithmia_ro'@'%';
```

`@'%'` aqui é aceitável, e só aqui: o container vive minutos, numa rede de compose que
é sua, e some depois. Num host **compartilhado** isso jamais valeria — ver §0.

Com `LEGADO_DB_HOST=legado` no `.env.producao`:

```bash
cd platform
export ALGORITHMIA_TAG="$(cat .deploy/tag-atual)"
docker compose -f compose.prod.yml run --rm --no-deps -e ALGORITHMIA_PULAR_OTIMIZACAO=1 \
    app php artisan algorithmia:importar --dry-run    # confira a reconciliação
docker compose -f compose.prod.yml run --rm --no-deps -e ALGORITHMIA_PULAR_OTIMIZACAO=1 \
    app php artisan algorithmia:importar
docker rm -f legado                                   # o efêmero morre aqui
```

> **Antes do `mysqldump`, rode `php database/migrate.php` no legado** (§8, passo 0). O
> importador copia o estado que encontrar: um legado atrasado migra os `mestres` e
> `itens` duplicados junto.

### 10.6 Provar, ainda sem DNS

```bash
docker compose -f compose.prod.yml exec app php artisan algorithmia:smoke   # sem --sem-conteudo
curl -k --resolve algorithmia.exemplo.com:443:127.0.0.1 https://algorithmia.exemplo.com/
```

O smoke **varre todas as instituições ativas**, uma a uma, e reprova se qualquer uma
estiver injogável. Ele é o portão do `deploy.sh`: um deploy que deixa uma escola sem
conteúdo não pode ser promovido. Use `--tenant=slug` para restringir.

Entre no jogo você mesmo, pelo `--resolve` ou por um `/etc/hosts` na sua máquina:
login, mapa, uma batalha. **O smoke não testa a sessão sob HTTPS**, e foi exatamente ali
que se escondeu o pior bug da tenancy: o `Authenticate` rodava antes do resolvedor de
tenant, o RLS o cegava, e o jogador logava para cair na tela de login de novo.

### 10.6b Uma segunda instituição

A ordem não é negociável, e o `DEFAULT` da coluna a impõe:

```sql
-- 1. Provisionar. Ela nasce DESLIGADA: sem conteúdo, é injogável, e uma escola ativa e
--    injogável reprova o smoke — ou seja, trava todos os deploys.
INSERT INTO tenants (nome, slug) VALUES ('Escola Nova', 'escola-nova');
INSERT INTO tenant_dominios (tenant_id, host, primario) VALUES (2, 'nova.exemplo.com', true);
```

```bash
# 2. Semear o conteúdo dela. `--tenant` é obrigatório com mais de uma instituição:
#    importar para a escola errada é irreversível sem restaurar dump.
docker compose -f compose.prod.yml run --rm --no-deps app \
    php artisan algorithmia:importar --tenant=escola-nova
```

```sql
-- 3. Só então ligar.
UPDATE tenants SET ativo = true WHERE slug = 'escola-nova';
```

> **`--truncar` recusa rodar com mais de uma instituição no banco.** `TRUNCATE` é uma
> operação de tabela, não de linha: ele **ignora o RLS** e apagaria todas as escolas. A
> policy nem seria consultada.

### 10.7 O corte é o DNS

Baixe o TTL do registro **24h antes**. Então aponte o A/AAAA para a VPS nova.

A ordem importa, e é a mesma do §8: o legado vai a **somente-leitura primeiro**, depois
o dump, depois a importação. O que um jogador escrever no legado entre o dump e a troca
de DNS **não chega ao port**.

**Rollback:** devolva o DNS ao host antigo e restaure a escrita no legado. É por isso
que ele fica de pé por uma janela combinada — ele é o plano de rollback de verdade. O
que os jogadores criarem no port nesse intervalo não volta.

### 10.8 Depois

- `bin/backup.sh` num cron diário; `bin/restore.sh --ensaio` de tempos em tempos —
  backup que nunca foi restaurado não é backup, é esperança.
- `TRUSTED_PROXIES` continua vazio. Só volte a preenchê-lo se puser um proxy na frente.
