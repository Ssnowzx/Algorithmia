#!/usr/bin/env bash
#
# Deploy do Algorithmia (port em Laravel) numa VPS com Docker.
#
# A imagem é etiquetada com o SHA do commit. Rollback é apontar a tag anterior e
# subir de novo — sem rebuild, sem `git checkout` no servidor, sem surpresa.
#
#   bin/deploy.sh              # deploy do HEAD
#   bin/deploy.sh --sem-backup # pula o dump (use só se acabou de tirar um)
#
# ATENÇÃO ÀS MIGRATIONS
# ---------------------
# Elas rodam ANTES de o código novo subir, contra o código VELHO ainda no ar.
# Isso só é seguro se cada migration for aditiva (expand): criar tabela, criar
# coluna anulável, criar índice. Remover ou renomear coluna quebra o código velho
# no intervalo, e o rollback não desfaz migration — desfaz-se restaurando o dump.
# Se precisar remover algo, faça em DOIS deploys: primeiro pare de usar, depois
# remova.

set -euo pipefail

RAIZ="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PLATAFORMA="${RAIZ}/platform"
ESTADO="${PLATAFORMA}/.deploy"
COMPOSE="docker compose -f ${PLATAFORMA}/compose.prod.yml"

FAZER_BACKUP=1
[[ "${1:-}" == "--sem-backup" ]] && FAZER_BACKUP=0

erro() { printf '\033[31m✗ %s\033[0m\n' "$*" >&2; exit 1; }
passo() { printf '\n\033[1m→ %s\033[0m\n' "$*"; }
ok()   { printf '\033[32m  ✓ %s\033[0m\n' "$*"; }

# ---------------------------------------------------------------- pré-condições
passo "Verificando pré-condições"

command -v docker >/dev/null || erro "docker não encontrado"
[[ -f "${PLATAFORMA}/.env.producao" ]] || erro "falta platform/.env.producao (veja .env.producao.exemplo)"
[[ -f "${PLATAFORMA}/.env.producao.postgres" ]] || erro "falta platform/.env.producao.postgres"

# Um deploy a partir de uma árvore suja é um deploy que ninguém consegue reproduzir.
if [[ -n "$(git -C "${RAIZ}" status --porcelain)" ]]; then
    erro "a árvore de trabalho tem alterações não commitadas"
fi

TAG="$(git -C "${RAIZ}" rev-parse --short HEAD)"
ok "commit ${TAG}"

mkdir -p "${ESTADO}" "${RAIZ}/backups"

TAG_ANTERIOR=""
[[ -f "${ESTADO}/tag-atual" ]] && TAG_ANTERIOR="$(cat "${ESTADO}/tag-atual")"

# ---------------------------------------------------------------------- backup
if [[ "${FAZER_BACKUP}" == "1" ]] && $COMPOSE ps postgres --status running -q | grep -q .; then
    passo "Backup do banco antes de tocar em qualquer coisa"
    "${RAIZ}/bin/backup.sh" "pre-${TAG}"
    ok "dump gravado"
else
    passo "Backup pulado (primeiro deploy ou --sem-backup)"
fi

# ----------------------------------------------------------------------- build
passo "Construindo a imagem algorithmia:${TAG}"
docker build \
    --file "${PLATAFORMA}/docker/php/Dockerfile" \
    --tag "algorithmia:${TAG}" \
    "${RAIZ}"
ok "imagem pronta"

# ------------------------------------------------------------------- migrations
passo "Aplicando migrations (aditivas — ver o cabeçalho deste script)"
ALGORITHMIA_TAG="${TAG}" $COMPOSE up -d postgres
ALGORITHMIA_TAG="${TAG}" ALGORITHMIA_PULAR_OTIMIZACAO=1 \
    $COMPOSE run --rm --no-deps app php artisan migrate --force
ok "schema atualizado"

# -------------------------------------------------------------------- subir app
passo "Subindo app e web na tag ${TAG}"
ALGORITHMIA_TAG="${TAG}" $COMPOSE up -d --remove-orphans
ok "containers no ar"

# ------------------------------------------------------------------ verificação
passo "Esperando o health check"
for _ in $(seq 1 30); do
    if ALGORITHMIA_TAG="${TAG}" $COMPOSE ps web --format '{{.Health}}' 2>/dev/null | grep -q healthy; then
        ok "web saudável"
        break
    fi
    sleep 2
done

passo "Smoke: o jogo está jogável?"
if ! ALGORITHMIA_TAG="${TAG}" $COMPOSE exec -T app php artisan algorithmia:smoke; then
    printf '\n\033[31m✗ O smoke reprovou este deploy.\033[0m\n' >&2

    if [[ -n "${TAG_ANTERIOR}" ]]; then
        printf '  Revertendo para %s…\n' "${TAG_ANTERIOR}" >&2
        ALGORITHMIA_TAG="${TAG_ANTERIOR}" $COMPOSE up -d
        printf '  Código revertido. O SCHEMA NÃO FOI: se a migration não era aditiva,\n' >&2
        printf '  restaure o dump com  bin/restore.sh backups/pre-%s-*.sql.gz\n' "${TAG}" >&2
    else
        printf '  Não há versão anterior para reverter. Investigue antes de expor.\n' >&2
    fi
    exit 1
fi

# -------------------------------------------------------------------- registrar
[[ -n "${TAG_ANTERIOR}" ]] && printf '%s' "${TAG_ANTERIOR}" > "${ESTADO}/tag-anterior"
printf '%s' "${TAG}" > "${ESTADO}/tag-atual"

printf '\n\033[32m✓ Deploy de %s concluído.\033[0m\n' "${TAG}"
[[ -n "${TAG_ANTERIOR}" ]] && printf '  Rollback: bin/rollback.sh (volta para %s)\n' "${TAG_ANTERIOR}"
exit 0
