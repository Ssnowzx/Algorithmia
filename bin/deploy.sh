#!/usr/bin/env bash
#
# Deploy do Algorithmia (port em Laravel) numa VPS com Docker.
#
# A imagem é etiquetada com o SHA do commit. Rollback é apontar a tag anterior e
# subir de novo — sem rebuild, sem `git checkout` no servidor, sem surpresa.
#
#   bin/deploy.sh                # deploy do HEAD
#   bin/deploy.sh --sem-backup   # pula o dump (use só se acabou de tirar um)
#   bin/deploy.sh --sem-conteudo # banco ainda vazio (só no PRIMEIRO deploy, antes
#                                # de `algorithmia:importar` — ver RUNBOOK §2 e §8)
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

# Topologia da máquina (portas, conf do nginx, certificados), se houver. Guardá-la em
# arquivo em vez de exportá-la à mão é o que impede um `rollback.sh` de amanhã de
# devolver o site a HTTP na 8080 sem avisar. Ver RUNBOOK §10.
# shellcheck source=/dev/null
[[ -f "${ESTADO}/ambiente" ]] && . "${ESTADO}/ambiente"

FAZER_BACKUP=1
# No primeiro deploy o banco está vazio: a importação só roda depois, e ela precisa
# do container `app` de pé. O smoke completo exigiria o conteúdo que ainda não
# chegou. A flag é explícita de propósito — se o deploy adivinhasse "tabela vazia,
# então pule a checagem", um deploy que PERDEU o conteúdo passaria calado.
SMOKE_ARGS=()
for arg in "$@"; do
    case "${arg}" in
        --sem-backup)   FAZER_BACKUP=0 ;;
        --sem-conteudo) SMOKE_ARGS+=(--sem-conteudo) ;;
        *) printf '✗ opção desconhecida: %s\n' "${arg}" >&2; exit 1 ;;
    esac
done

erro() { printf '\033[31m✗ %s\033[0m\n' "$*" >&2; exit 1; }
passo() { printf '\n\033[1m→ %s\033[0m\n' "$*"; }
ok()   { printf '\033[32m  ✓ %s\033[0m\n' "$*"; }

# Prontidão de verdade: `/healthz` respondendo 200 através do nginx. O status do
# container diz apenas que o processo subiu — o entrypoint ainda leva alguns
# segundos gerando os caches, e nesse intervalo o nginx devolve 502.
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
# `--wait` espera o healthcheck, não só o start. No primeiro deploy o volume está
# vazio, o postgres roda `initdb` antes de escutar, e o `migrate` logo abaixo usa
# `--no-deps` — que manda o compose ignorar o `depends_on: service_healthy`. Sem
# esperar aqui, o primeiro deploy morre com "connection refused"; do segundo em
# diante o volume já existe e o problema some.
ALGORITHMIA_TAG="${TAG}" $COMPOSE up -d --wait postgres
ALGORITHMIA_TAG="${TAG}" ALGORITHMIA_PULAR_OTIMIZACAO=1 \
    $COMPOSE run --rm --no-deps app php artisan migrate --force
ok "schema atualizado"

# -------------------------------------------------------------------- subir app
passo "Subindo app e web na tag ${TAG}"
ALGORITHMIA_TAG="${TAG}" $COMPOSE up -d --remove-orphans
ok "containers no ar"

# ------------------------------------------------------------------ verificação
passo "Esperando /healthz responder através do nginx"
esperar_saudavel "${TAG}" || erro "o app não ficou saudável em 90s"
ok "saudável"

passo "Smoke: o jogo está jogável?"
# `${a[@]+"${a[@]}"}`: no bash 3.2 (macOS), `set -u` trata a expansão de um array
# vazio como variável não-definida e aborta. Na VPS (bash 5) passaria batido.
if ! ALGORITHMIA_TAG="${TAG}" $COMPOSE exec -T app php artisan algorithmia:smoke \
        ${SMOKE_ARGS[@]+"${SMOKE_ARGS[@]}"}; then
    printf '\n\033[31m✗ O smoke reprovou este deploy.\033[0m\n' >&2

    if [[ -z "${TAG_ANTERIOR}" ]]; then
        printf '  Não há versão anterior para reverter. Investigue ANTES de expor.\n' >&2
        exit 1
    fi

    printf '  Revertendo para %s…\n' "${TAG_ANTERIOR}" >&2
    ALGORITHMIA_TAG="${TAG_ANTERIOR}" $COMPOSE up -d

    # Reverter e sair sem esperar deixaria o operador achando que está tudo bem
    # enquanto o nginx ainda devolve 502 — o entrypoint leva alguns segundos.
    if ! esperar_saudavel "${TAG_ANTERIOR}"; then
        printf '\033[31m  ✗ A versão anterior TAMBÉM não sobe. O site está fora. Aja agora.\033[0m\n' >&2
        exit 1
    fi

    if ! ALGORITHMIA_TAG="${TAG_ANTERIOR}" $COMPOSE exec -T app php artisan algorithmia:smoke --rasa; then
        printf '\033[31m  ✗ A versão anterior reprova o smoke. O problema é o BANCO, não o código.\033[0m\n' >&2
        printf '    Restaure o dump: bin/restore.sh backups/pre-%s-*.sql.gz\n' "${TAG}" >&2
        exit 1
    fi

    printf '\033[33m  ⚠ Código revertido para %s e no ar.\033[0m\n' "${TAG_ANTERIOR}" >&2
    printf '    O SCHEMA NÃO FOI revertido. Se a migration de %s não era aditiva,\n' "${TAG}" >&2
    printf '    restaure o dump: bin/restore.sh backups/pre-%s-*.sql.gz\n' "${TAG}" >&2
    exit 1
fi

# -------------------------------------------------------------------- registrar
[[ -n "${TAG_ANTERIOR}" ]] && printf '%s' "${TAG_ANTERIOR}" > "${ESTADO}/tag-anterior"
printf '%s' "${TAG}" > "${ESTADO}/tag-atual"

printf '\n\033[32m✓ Deploy de %s concluído.\033[0m\n' "${TAG}"
[[ -n "${TAG_ANTERIOR}" ]] && printf '  Rollback: bin/rollback.sh (volta para %s)\n' "${TAG_ANTERIOR}"
exit 0
