#!/bin/sh
set -e

# Falhar aqui, alto e cedo, é melhor do que servir um app que não consegue
# descriptografar a sessão de ninguém. O `config:cache` NÃO valida isto sozinho:
# ele aceita APP_KEY vazio sem reclamar, e o erro só aparece no primeiro login.
if [ -z "${APP_KEY:-}" ] && [ "${ALGORITHMIA_PULAR_OTIMIZACAO:-0}" != "1" ]; then
    echo "ERRO: APP_KEY está vazio. Gere uma com:" >&2
    echo "  docker compose -f compose.prod.yml run --rm app php artisan key:generate --show" >&2
    exit 1
fi

# Um APP_DEBUG ligado em produção entrega caminhos de arquivo e trechos de código
# a qualquer aluno que provoque um erro.
if [ "${APP_ENV:-}" = "production" ] && [ "${APP_DEBUG:-false}" = "true" ]; then
    echo "ERRO: APP_DEBUG=true com APP_ENV=production." >&2
    exit 1
fi

# Os caches de config, rota e view dependem do .env, que só existe no host. Por
# isso são gerados aqui, e não no build da imagem.
if [ "${ALGORITHMIA_PULAR_OTIMIZACAO:-0}" != "1" ]; then
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

exec "$@"
