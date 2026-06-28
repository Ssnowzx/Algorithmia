#!/usr/bin/env bash
# Sobe o bridge Telegram <-> Claude Code.
# Uso: ./start.sh   (ou: node bridge.mjs)
set -euo pipefail
cd "$(dirname "$0")"

if [ ! -f .env ]; then
  echo "⚠️  Sem .env. Rode: cp .env.example .env  e preencha o TELEGRAM_BOT_TOKEN."
  exit 1
fi

exec node bridge.mjs
