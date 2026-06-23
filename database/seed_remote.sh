#!/usr/bin/env bash
#
# Provisiona um banco MariaDB/MySQL remoto a partir do ZERO:
#   schema.sql  → tabelas (CREATE TABLE IF NOT EXISTS, não-destrutivo)
#   seeds.sql   → dados-base (mestres, fases, conquistas, itens, ...)
#   banco-questoes.sql → as 157 perguntas ampliadas (anti-repetição)
#
# Pula a linha "CREATE DATABASE" e o "USE" (útil quando o usuário só tem
# privilégio na database, não global). Pede a senha uma única vez.
#
# ⚠️  Para um banco que JÁ TEM jogadores, NÃO rode este script inteiro:
#     reimportar seeds.sql pode duplicar/colidir dados-base. Nesse caso,
#     aplique APENAS as perguntas novas (idempotente, seguro):
#       mariadb -h HOST -u USUARIO -p --ssl=0 BANCO < database/banco-questoes.sql
#     (ou, na própria VPS:  php database/seed-banco-questoes.php)
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

echo "→ Importando schema + seeds + banco de questões em ${USER}@${HOST}/${DB} ..."

# Remove o bloco CREATE DATABASE (linhas até o ';') e qualquer linha USE,
# para importar no banco passado em $DB (e não num nome fixo do arquivo).
cat \
  <(sed -e '/^CREATE DATABASE/,/;/d' -e '/^USE /d' "${DIR}/schema.sql") \
  "${DIR}/seeds.sql" \
  "${DIR}/banco-questoes.sql" \
  | mariadb -h "${HOST}" -u "${USER}" -p --ssl=0 "${DB}"

echo "✅ Import concluído."
echo "   Confira:  SELECT COUNT(*) FROM mestres;  SELECT fase_id, COUNT(*) FROM desafios GROUP BY fase_id;"
