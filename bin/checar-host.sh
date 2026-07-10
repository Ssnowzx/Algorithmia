#!/usr/bin/env bash
# Diagnóstico do host, antes do corte. NÃO instala, NÃO altera, NÃO escreve.
# Rode na VPS:  bash checar-host.sh

linha() { printf '\n\033[1m── %s\033[0m\n' "$*"; }
tem()   { command -v "$1" >/dev/null 2>&1 && echo "  ✓ $1: $($1 --version 2>&1 | head -1)" || echo "  ✗ $1: NÃO existe"; }

linha "Quem sou eu, e o sistema"
echo "  usuário: $(id -un)  (uid=$(id -u))"
[ "$(id -u)" -eq 0 ] && echo "  ✓ sou root" || { sudo -n true 2>/dev/null && echo "  ✓ tenho sudo sem senha" || echo "  ? sem root direto — teste 'sudo -v'"; }
echo "  distro: $(. /etc/os-release 2>/dev/null && echo "$PRETTY_NAME" || echo desconhecida)"
[ -d /usr/local/cpanel ] && echo "  ⚠ cPanel DETECTADO (/usr/local/cpanel) — vhost editado à mão é sobrescrito"

linha "Docker (o plano do port depende dele)"
tem docker
docker info >/dev/null 2>&1 && echo "  ✓ o daemon responde" || echo "  ✗ daemon NÃO responde (não instalado, parado, ou sem permissão)"
echo "  compose: $(docker compose version --short 2>/dev/null || echo 'NÃO existe')"
echo "  (bin/deploy.sh usa 'up -d --wait' → exige compose >= 2.1.1)"

linha "Servidor web e proxy"
for s in httpd apache2; do systemctl is-active "$s" >/dev/null 2>&1 && echo "  ✓ $s ativo"; done
(httpd -M 2>/dev/null || apache2ctl -M 2>/dev/null) | grep -qE "proxy_http" \
  && echo "  ✓ mod_proxy_http carregado" || echo "  ✗ mod_proxy_http NÃO carregado (sem ele, não há proxy reverso)"
echo "  quem escuta na 80/443:"; (ss -lntp 2>/dev/null || netstat -lntp 2>/dev/null) | grep -E ':(80|443) ' | sed 's/^/    /'

linha "Portas altas livres (o nginx do port precisa de uma)"
# Sem ferramenta, um grep vazio pareceria "porta livre". Prefiro não responder.
# Só o `ss` do iproute2. O netstat do BSD/macOS ignora o -l e lista CONEXÕES em vez
# de sockets em escuta: a saída vem cheia, com o significado errado, e o check diria
# "livre" para uma porta ocupada. Uma resposta errada é pior que nenhuma.
if command -v ss >/dev/null 2>&1; then
  PORTAS="$(ss -lnt 2>/dev/null)"
  for p in 8080 8081 8090; do
    printf '%s' "$PORTAS" | grep -qE "[:.]$p[[:space:]]" && echo "  ✗ $p ocupada" || echo "  ✓ $p livre"
  done
else
  echo "  ? 'ss' (iproute2) não existe aqui — não vou adivinhar. NÃO assuma que estão livres."
fi

linha "MySQL do legado — a importação fala com ele pelo SOCKET, não pela rede"
SOCKET="$(mysqladmin variables 2>/dev/null | awk -F'|' '$2 ~ /^ *socket *$/ {gsub(/ /,"",$3); print $3}')"
if [ -n "${SOCKET}" ]; then
  echo "  ✓ socket: ${SOCKET}"
  echo "    monte-o no container: -v ${SOCKET}:/var/run/mysqld/mysqld.sock"
  echo "    e defina LEGADO_DB_SOCKET=/var/run/mysqld/mysqld.sock"
else
  echo "  ? não consegui ler o socket (precisa de credenciais):  mysqladmin variables | grep socket"
fi
echo "  onde a 3306 escuta hoje:"
ss -lntp 2>/dev/null | grep ':3306' | sed 's/^/    /' || echo "    (não encontrada)"
echo "  ↑ 0.0.0.0 = exposta à rede. NÃO abra a 3306 para importar: use o socket acima."
echo "    Num host compartilhado, 'GRANT ... @172.%' alcança containers de outros donos."

linha "Estado do banco do legado (migrations aplicadas)"
echo "  rode, com as credenciais do site:"
echo "    mysql -u USUARIO -p BANCO -e 'SELECT arquivo FROM migracoes_aplicadas ORDER BY 1;'"
echo "  esperado (6): novas-classes, assunto-calculo, marcio-lorde-segfault,"
echo "                objetivos-loja, dedupe-itens, dedupe-mestres"
echo "  faltando alguma → rode 'php database/migrate.php' ANTES de trancar o legado"

linha "Disco e RAM — a RAM é o gargalo real, não o Docker"
df -h / 2>/dev/null | tail -1 | awk '{print "  / → "$4" livres de "$2}'

# A RAM disponível é que decide, não a total: o host pode já estar carregando outros
# serviços. `docker build` do PHP (composer install + extensões) chega perto de 1 GiB
# de pico, e depois postgres + php-fpm + nginx querem uns 600 MiB em repouso.
# `MEMINFO` só existe para os testes: `-m 512m` no docker não altera /proc/meminfo,
# então sem esta costura os três ramos abaixo nunca seriam exercitados.
MEMINFO="${MEMINFO:-/proc/meminfo}"
DISP_MB="$(awk '/MemAvailable/ {printf "%d", $2/1024}' "${MEMINFO}" 2>/dev/null)"
if [ -n "${DISP_MB}" ]; then
  echo "  RAM → ${DISP_MB} MiB disponíveis"
  if [ "${DISP_MB}" -lt 1024 ]; then
    echo "  ✗ menos de 1 GiB disponível: o 'docker build' provavelmente será morto pelo OOM."
    echo "    Saída: construa a imagem noutra máquina e traga pronta —"
    echo "      docker save algorithmia:TAG | ssh este-host 'docker load'"
  elif [ "${DISP_MB}" -lt 2048 ]; then
    echo "  ⚠ entre 1 e 2 GiB: o build passa raspando. Tenha swap, ou construa fora."
  else
    echo "  ✓ RAM suficiente para construir e rodar."
  fi
  SWAP_MB="$(awk '/SwapTotal/ {printf "%d", $2/1024}' "${MEMINFO}" 2>/dev/null)"
  echo "  swap → ${SWAP_MB:-0} MiB"
else
  echo "  ? sem /proc/meminfo — não vou adivinhar a RAM."
fi

printf '\n\033[1mVeredito:\033[0m o corte com Docker exige TODOS: root, "✓ docker",\n'
printf '"✓ daemon responde", compose >= 2.1.1, "✓ mod_proxy_http", uma porta alta livre\n'
printf 'e >= 1 GiB de RAM disponível (ou a imagem construída fora). Faltando um, o\n'
printf 'RUNBOOK §8 não roda como está escrito.\n'
