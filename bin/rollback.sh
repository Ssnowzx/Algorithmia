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

# Topologia da máquina (portas, conf do nginx, certificados). Sem isto, um rollback
# no host de TLS direto subiria o site de volta em HTTP na 8080 — calado, porque os
# padrões do compose são exatamente esses. Ver RUNBOOK §10.
# shellcheck source=/dev/null
[[ -f "${ESTADO}/ambiente" ]] && . "${ESTADO}/ambiente"

erro() { printf '\033[31m✗ %s\033[0m\n' "$*" >&2; exit 1; }
passo() { printf '\n\033[1m→ %s\033[0m\n' "$*"; }
ok()   { printf '\033[32m  ✓ %s\033[0m\n' "$*"; }

# Prontidão de verdade: `/healthz` através do nginx. O status do container só diz
# que o processo subiu; o entrypoint ainda leva segundos gerando os caches.
esperar_saudavel() {
    local tag="$1" tentativa corpo
    for tentativa in $(seq 1 45); do
        # Duas armadilhas evitadas aqui:
        #  - Sem o pipe: com `set -o pipefail`, um `| grep -q` faz o grep fechar o
        #    cano ao casar, o `docker compose exec` morre de SIGPIPE (141), e o
        #    pipefail propaga esse 141 — a condição jamais seria verdadeira.
        #  - 127.0.0.1, e não `localhost`: o wget do BusyBox tenta ::1 primeiro, e
        #    este nginx só escuta em IPv4.
        corpo="$(ALGORITHMIA_TAG="${tag}" $COMPOSE exec -T web \
                    wget -qO- http://127.0.0.1/healthz 2>/dev/null || true)"

        if [[ "${corpo}" == *'"status":"ok"'* ]]; then
            return 0
        fi
        sleep 2
    done
    return 1
}

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

passo "Esperando /healthz responder através do nginx"
esperar_saudavel "${ALVO}" || erro "a versão ${ALVO} não ficou saudável em 90s. O site está fora."
ok "saudável"

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
