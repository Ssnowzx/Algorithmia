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

linha "MySQL do legado — o import roda dentro de um container e precisa alcançá-lo"
echo "  bind-address:"; (ss -lntp 2>/dev/null | grep ':3306') | sed 's/^/    /' || echo "    (3306 não encontrada)"
echo "  ↑ se aparecer só 127.0.0.1, o container NÃO alcança o MySQL"

linha "Estado do banco do legado (migrations aplicadas)"
echo "  rode, com as credenciais do site:"
echo "    mysql -u USUARIO -p BANCO -e 'SELECT arquivo FROM migracoes_aplicadas ORDER BY 1;'"
echo "  esperado (6): novas-classes, assunto-calculo, marcio-lorde-segfault,"
echo "                objetivos-loja, dedupe-itens, dedupe-mestres"
echo "  faltando alguma → rode 'php database/migrate.php' ANTES de trancar o legado"

linha "Espaço em disco (o build da imagem precisa de folga)"
df -h / 2>/dev/null | tail -1 | awk '{print "  / → "$4" livres de "$2}'
free -h 2>/dev/null | awk '/Mem:/{print "  RAM → "$7" disponíveis de "$2}'

printf '\n\033[1mVeredito:\033[0m sem "✓ docker" + "✓ daemon responde" + "✓ mod_proxy_http",\n'
printf 'o corte com Docker descrito no RUNBOOK NÃO roda neste host.\n'
