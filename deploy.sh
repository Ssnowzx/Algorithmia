#!/usr/bin/env bash
#
# Deploy do Algorithmia em hospedagem gerenciada (sem precisar de root/Apache).
# Coloca o código no document root e grava as credenciais do banco num arquivo
# local fora do git (config/db.local.php). Web e banco estão na mesma máquina,
# então o app conecta no MySQL/MariaDB via 127.0.0.1.
#
# COMO USAR (na VPS, dentro do document root do site — ex: a pasta servida em
# algorithmia.tars.art.br):
#
#   curl -fsSL https://raw.githubusercontent.com/Ssnowzx/Algorithmia/main/deploy.sh -o deploy.sh
#   bash deploy.sh
#
# Ou rode apontando o destino:   bash deploy.sh /caminho/do/docroot
#
# Atualizações futuras: rode de novo — faz git pull e preserva o db.local.php.
#
set -euo pipefail

REPO="https://github.com/Ssnowzx/Algorithmia.git"
TARGET="${1:-$PWD}"

DB_HOST="${DB_HOST:-127.0.0.1}"
DB_NAME="${DB_NAME:-algorithmia}"
DB_USER="${DB_USER:-algorithmia}"

echo "→ Destino (document root): ${TARGET}"

# ---- 1. Código: clona (se vazio) ou atualiza (se já é repo) ----------------
if [[ -d "${TARGET}/.git" ]]; then
  echo "→ Repositório existente — atualizando (git pull)..."
  git -C "${TARGET}" pull --ff-only
elif [[ -z "$(ls -A "${TARGET}" 2>/dev/null)" ]]; then
  echo "→ Pasta vazia — clonando o repositório..."
  git clone --depth 1 "${REPO}" "${TARGET}"
else
  echo "✖ '${TARGET}' não está vazio e não é um clone do repositório." >&2
  echo "  Esvazie o document root (apague o index.html padrão, etc.) e rode de novo," >&2
  echo "  ou rode dentro de uma subpasta vazia." >&2
  exit 1
fi

# ---- 2. Credenciais do banco (arquivo local, fora do git) ------------------
LOCAL_CFG="${TARGET}/config/db.local.php"
if [[ -f "${LOCAL_CFG}" ]]; then
  echo "→ Mantendo credenciais existentes em config/db.local.php"
else
  if [[ -z "${DB_PASS:-}" ]]; then
    read -rsp "Senha do banco (usuário ${DB_USER}): " DB_PASS; echo
  fi
  cat > "${LOCAL_CFG}" <<PHP
<?php
// Credenciais de produção — NÃO versionado (veja .gitignore).
// Gerado por deploy.sh. O config/db.php carrega este arquivo automaticamente.
putenv('DB_HOST=${DB_HOST}');
putenv('DB_NAME=${DB_NAME}');
putenv('DB_USER=${DB_USER}');
putenv('DB_PASS=${DB_PASS}');
PHP
  chmod 600 "${LOCAL_CFG}" 2>/dev/null || true
  echo "→ Credenciais gravadas em config/db.local.php"
fi

# ---- 3. Teste rápido de conexão (se o PHP CLI estiver disponível) ----------
if command -v php >/dev/null 2>&1; then
  echo "→ Testando conexão com o banco..."
  if php -r '
    require "'"${TARGET}"'/config/db.php";
    $n = getConnection()->query("SELECT COUNT(*) FROM mestres")->fetchColumn();
    echo "   OK — {$n} mestres no banco.\n";
  ' 2>/dev/null; then
    :
  else
    echo "   ⚠ Não consegui validar pelo CLI (pode ser normal se o CLI usa outro host)." >&2
    echo "     Teste pelo navegador: https://algorithmia.tars.art.br/" >&2
  fi
fi

echo
echo "✅ Deploy concluído. Acesse:  https://algorithmia.tars.art.br/"
echo "   Se as rotas derem 404, o mod_rewrite/.htaccess pode estar desativado —"
echo "   me avise que ajusto."
