#!/usr/bin/env bash
#
# Volta o Algorithmia para a versão anterior.
#
#   bin/rollback.sh                    # volta o CÓDIGO para a tag anterior
#   bin/rollback.sh --com-banco DUMP   # volta o código E restaura o banco
#
# O QUE ISTO NÃO FAZ
# ------------------
# Não desfaz migrations. Trocar a tag da imagem devolve o código antigo, mas o
# schema continua o novo. Se a migration do deploy ruim foi aditiva (criar tabela
# ou coluna anulável), o código antigo simplesmente a ignora e tudo funciona. Se
# ela removeu ou renomeou algo, o código antigo quebra — e aí é preciso restaurar
# o dump, perdendo o que os jogadores fizeram desde então.
#
# É por isso que toda migration deve ser aditiva. Ver bin/deploy.sh.

set -euo pipefail

RAIZ="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PLATAFORMA="${RAIZ}/platform"
ESTADO="${PLATAFORMA}/.deploy"
COMPOSE="docker compose -f ${PLATAFORMA}/compose.prod.yml"

erro() { printf '\033[31m✗ %s\033[0m\n' "$*" >&2; exit 1; }
passo() { printf '\n\033[1m→ %s\033[0m\n' "$*"; }
ok()   { printf '\033[32m  ✓ %s\033[0m\n' "$*"; }

[[ -f "${ESTADO}/tag-anterior" ]] || erro "não há tag anterior registrada em ${ESTADO}"

ALVO="$(cat "${ESTADO}/tag-anterior")"
ATUAL="$(cat "${ESTADO}/tag-atual" 2>/dev/null || echo '?')"

# Sem esta guarda, um rollback logo após outro seria um no-op silencioso: o script
# "reverteria" para a versão que já está no ar e declararia sucesso.
[[ "${ALVO}" != "${ATUAL}" ]] || erro "a tag anterior (${ALVO}) já é a que está no ar. Faça um deploy antes de reverter de novo."

docker image inspect "algorithmia:${ALVO}" >/dev/null 2>&1 \
    || erro "a imagem algorithmia:${ALVO} não existe mais neste host"

passo "Revertendo ${ATUAL} → ${ALVO}"

if [[ "${1:-}" == "--com-banco" ]]; then
    DUMP="${2:-}"
    [[ -f "${DUMP}" ]] || erro "informe o dump: bin/rollback.sh --com-banco backups/pre-XXXX.sql.gz"

    printf '\033[33m  ⚠ Restaurar o banco DESCARTA tudo o que os jogadores fizeram\n' >&2
    printf '    desde o dump. Digite RESTAURAR para confirmar: \033[0m' >&2
    read -r confirmacao
    [[ "${confirmacao}" == "RESTAURAR" ]] || erro "cancelado"

    "${RAIZ}/bin/restore.sh" "${DUMP}"
    ok "banco restaurado"
fi

ALGORITHMIA_TAG="${ALVO}" $COMPOSE up -d
ok "containers na tag ${ALVO}"

passo "Esperando o health check"
for _ in $(seq 1 30); do
    if ALGORITHMIA_TAG="${ALVO}" $COMPOSE ps web --format '{{.Health}}' 2>/dev/null | grep -q healthy; then
        ok "web saudável"
        break
    fi
    sleep 2
done

passo "Smoke"
ALGORITHMIA_TAG="${ALVO}" $COMPOSE exec -T app php artisan algorithmia:smoke \
    || erro "a versão anterior TAMBÉM reprova o smoke. Investigue o banco."

printf '%s' "${ALVO}" > "${ESTADO}/tag-atual"

# Não há "versão anterior à anterior" conhecida como boa. Apagar o marcador é mais
# honesto do que deixá-lo apontando para a tag que acabou de subir — e obriga um
# deploy antes de um novo rollback.
rm "${ESTADO}/tag-anterior"
printf '%s' "${ATUAL}" > "${ESTADO}/tag-rejeitada"

printf '\n\033[32m✓ Rollback para %s concluído.\033[0m\n' "${ALVO}"
printf '  A tag %s foi abandonada (registrada em .deploy/tag-rejeitada).\n' "${ATUAL}"
printf '  Se a migration dela NÃO era aditiva, restaure o dump:\n'
printf '    bin/restore.sh backups/pre-%s-*.sql.gz\n' "${ATUAL}"
