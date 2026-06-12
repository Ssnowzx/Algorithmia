#!/usr/bin/env bash
#
# Setup de produção do Algorithmia numa VPS Ubuntu/Debian com Apache.
# Instala o stack, configura o VirtualHost (com as credenciais do banco via
# SetEnv) e ativa o site. O banco MariaDB/MySQL deve já existir e estar semeado.
#
# Uso (na VPS, como root ou com sudo):
#   sudo bash tools/setup_vps.sh
#
# Variáveis de banco (sobrescreva via ambiente se quiser):
#   DB_HOST (padrão 127.0.0.1)  DB_NAME (algorithmia)  DB_USER (algorithmia)
#   DB_PASS é solicitado interativamente se não vier no ambiente.
#
set -euo pipefail

if [[ $EUID -ne 0 ]]; then
  echo "✖ Rode como root:  sudo bash tools/setup_vps.sh" >&2
  exit 1
fi

APP_DIR="$(cd "$(dirname "$0")/.." && pwd)"
DB_HOST="${DB_HOST:-127.0.0.1}"
DB_NAME="${DB_NAME:-algorithmia}"
DB_USER="${DB_USER:-algorithmia}"

if [[ -z "${DB_PASS:-}" ]]; then
  read -rsp "Senha do banco (usuário ${DB_USER}): " DB_PASS; echo
fi

echo "→ Instalando Apache + PHP + extensões..."
export DEBIAN_FRONTEND=noninteractive
apt update -y
apt install -y apache2 libapache2-mod-php php php-mysql php-mbstring git
a2enmod rewrite >/dev/null

echo "→ Ajustando dono dos arquivos para www-data..."
chown -R www-data:www-data "$APP_DIR"

echo "→ Escrevendo o VirtualHost..."
cat > /etc/apache2/sites-available/algorithmia.conf <<CONF
<VirtualHost *:80>
    DocumentRoot ${APP_DIR}

    SetEnv DB_HOST ${DB_HOST}
    SetEnv DB_NAME ${DB_NAME}
    SetEnv DB_USER ${DB_USER}
    SetEnv DB_PASS ${DB_PASS}

    <Directory ${APP_DIR}>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # Defesa extra: nega acesso web direto a pastas internas.
    <DirectoryMatch "${APP_DIR}/(config|app|database|tools|openspec|docs)">
        Require all denied
    </DirectoryMatch>

    ErrorLog  \${APACHE_LOG_DIR}/algorithmia-error.log
    CustomLog \${APACHE_LOG_DIR}/algorithmia-access.log combined
</VirtualHost>
CONF

echo "→ Ativando o site..."
a2dissite 000-default.conf >/dev/null 2>&1 || true
a2ensite algorithmia.conf >/dev/null
apache2ctl configtest
systemctl reload apache2

IP="$(hostname -I 2>/dev/null | awk '{print $1}')"
echo
echo "✅ Pronto! Acesse:  http://${IP:-SEU_IP}/"
echo "   Logs em /var/log/apache2/algorithmia-error.log se algo falhar."
