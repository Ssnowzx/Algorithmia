#!/usr/bin/env bash
#
# Dump do banco de produção.
#
#   bin/backup.sh                # backups/algorithmia-<data>.sql.gz
#   bin/backup.sh pre-a1b2c3     # backups/pre-a1b2c3-<data>.sql.gz
#
# Um backup que nunca foi restaurado não é um backup — é uma esperança. Restaure
# num banco descartável de tempos em tempos: `bin/restore.sh --ensaio DUMP`.

set -euo pipefail

RAIZ="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PLATAFORMA="${RAIZ}/platform"
COMPOSE="docker compose -f ${PLATAFORMA}/compose.prod.yml"
DESTINO="${RAIZ}/backups"

PREFIXO="${1:-algorithmia}"
CARIMBO="$(date -u +%Y%m%dT%H%M%SZ)"
ARQUIVO="${DESTINO}/${PREFIXO}-${CARIMBO}.sql.gz"

mkdir -p "${DESTINO}"

# As credenciais vêm do env do container: nunca da linha de comando, que fica no
# histórico do shell e na lista de processos.
$COMPOSE exec -T postgres sh -c \
    'pg_dump --clean --if-exists --no-owner --no-privileges -U "$POSTGRES_USER" -d "$POSTGRES_DB"' \
    | gzip -9 > "${ARQUIVO}"

# Um gzip truncado passa despercebido até o dia da restauração.
gzip -t "${ARQUIVO}" || { printf '\033[31m✗ dump corrompido\033[0m\n' >&2; exit 1; }

LINHAS="$(gzip -dc "${ARQUIVO}" | wc -l | tr -d ' ')"
[[ "${LINHAS}" -gt 50 ]] || { printf '\033[31m✗ dump suspeito: só %s linhas\033[0m\n' "${LINHAS}" >&2; exit 1; }

printf '\033[32m✓ %s (%s, %s linhas)\033[0m\n' \
    "${ARQUIVO#"${RAIZ}/"}" "$(du -h "${ARQUIVO}" | cut -f1)" "${LINHAS}"

# Retenção: 30 dumps. Sem isto, o disco enche e o deploy falha na hora errada.
ls -1t "${DESTINO}"/*.sql.gz 2>/dev/null | tail -n +31 | while read -r velho; do
    rm "${velho}" && printf '  (removido antigo: %s)\n' "$(basename "${velho}")"
done
