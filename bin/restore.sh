#!/usr/bin/env bash
#
# Restaura um dump no banco de produção — ou, com --ensaio, num banco descartável.
#
#   bin/restore.sh --ensaio backups/algorithmia-2026....sql.gz   # seguro, testa o dump
#   bin/restore.sh backups/algorithmia-2026....sql.gz            # DESTRUTIVO
#
# O ensaio é o motivo de este script existir. Um backup nunca restaurado não é um
# backup; é uma esperança. Rode o ensaio regularmente.

set -euo pipefail

RAIZ="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PLATAFORMA="${RAIZ}/platform"
COMPOSE="docker compose -f ${PLATAFORMA}/compose.prod.yml"
# O `compose` interpola a imagem do serviço `app` mesmo quando o comando só toca o
# postgres — sem a variável ele recusa o arquivo inteiro. Para operações de banco a
# tag é irrelevante; basta existir.
export ALGORITHMIA_TAG="${ALGORITHMIA_TAG:-$(cat "${PLATAFORMA}/.deploy/tag-atual" 2>/dev/null || echo 'irrelevante')}"


erro() { printf '\033[31m✗ %s\033[0m\n' "$*" >&2; exit 1; }
ok()   { printf '\033[32m  ✓ %s\033[0m\n' "$*"; }

ENSAIO=0
if [[ "${1:-}" == "--ensaio" ]]; then
    ENSAIO=1
    shift
fi

DUMP="${1:-}"
[[ -f "${DUMP}" ]] || erro "informe o arquivo de dump"
gzip -t "${DUMP}" || erro "o dump está corrompido"

if [[ "${ENSAIO}" == "1" ]]; then
    ALVO="ensaio_restauracao"
    printf '→ Ensaio: restaurando em "%s" (o banco de produção não é tocado)\n' "${ALVO}"

    $COMPOSE exec -T postgres sh -c \
        "psql -U \"\$POSTGRES_USER\" -d postgres -c 'DROP DATABASE IF EXISTS ${ALVO}' -c 'CREATE DATABASE ${ALVO}'" \
        >/dev/null

    gzip -dc "${DUMP}" | $COMPOSE exec -T postgres sh -c \
        "psql -q -v ON_ERROR_STOP=1 -U \"\$POSTGRES_USER\" -d ${ALVO}" >/dev/null
    ok "restaurado sem erro"

    # Contagens: um dump que restaura mas chega vazio não serve de nada.
    $COMPOSE exec -T postgres sh -c \
        "psql -U \"\$POSTGRES_USER\" -d ${ALVO} -c \"
            SELECT 'usuarios' t, COUNT(*) n FROM usuarios
            UNION ALL SELECT 'personagens', COUNT(*) FROM personagens
            UNION ALL SELECT 'fases', COUNT(*) FROM fases
            UNION ALL SELECT 'desafios', COUNT(*) FROM desafios
            UNION ALL SELECT 'progresso_fases', COUNT(*) FROM progresso_fases
            ORDER BY 1\""

    $COMPOSE exec -T postgres sh -c \
        "psql -U \"\$POSTGRES_USER\" -d postgres -c 'DROP DATABASE ${ALVO}'" >/dev/null
    ok "banco de ensaio removido"

    printf '\033[32m✓ O dump é restaurável.\033[0m\n'
    exit 0
fi

# ------------------------------------------------------------------ destrutivo
printf '\033[33m⚠ Isto SUBSTITUI o banco de produção pelo conteúdo de:\n' >&2
printf '    %s\n' "${DUMP}" >&2
printf '  Tudo o que os jogadores fizeram desde este dump será perdido.\n' >&2
printf '  Digite RESTAURAR para confirmar: \033[0m' >&2
read -r confirmacao
[[ "${confirmacao}" == "RESTAURAR" ]] || erro "cancelado"

# Um backup do estado atual, antes de sobrescrevê-lo. Se a restauração for um
# engano, ainda há para onde voltar.
"${RAIZ}/bin/backup.sh" "antes-da-restauracao"

# `--clean --if-exists` no dump já derruba os objetos antigos. `ON_ERROR_STOP`
# garante que uma falha no meio não deixe um banco meio restaurado.
gzip -dc "${DUMP}" | $COMPOSE exec -T postgres sh -c \
    'psql -q -v ON_ERROR_STOP=1 -U "$POSTGRES_USER" -d "$POSTGRES_DB"'

ok "banco restaurado"
printf '  Rode  bin/deploy.sh  ou  docker compose exec app php artisan algorithmia:smoke\n'
