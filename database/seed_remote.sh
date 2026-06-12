#!/usr/bin/env bash
#
# Importa schema + seeds num banco MariaDB/MySQL remoto já existente.
# Pula a linha "CREATE DATABASE" (útil quando o usuário só tem privilégio
# na database, não global). Pede a senha uma única vez.
#
# Uso:
#   bash database/seed_remote.sh [HOST] [USUARIO] [BANCO]
#   bash database/seed_remote.sh                      # usa os padrões abaixo
#
set -euo pipefail

HOST="${1:-129.121.33.89}"
USER="${2:-algorithmia}"
DB="${3:-algorithmia}"

DIR="$(cd "$(dirname "$0")" && pwd)"

echo "→ Importando schema + seeds em ${USER}@${HOST}/${DB} ..."

cat \
  <(sed '/^CREATE DATABASE/,/utf8mb4_unicode_ci;/d' "${DIR}/schema.sql") \
  "${DIR}/seeds.sql" \
  | mariadb -h "${HOST}" -u "${USER}" -p --ssl=0 "${DB}"

echo "✅ Import concluído. Confira com: SELECT COUNT(*) FROM mestres;"
