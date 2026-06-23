# Deploy do Algorithmia — Ubuntu/Debian + Apache

Guia de produção para subir o jogo numa VPS Ubuntu/Debian, servindo via Apache,
acessível por IP (sem HTTPS). Stack: PHP 8 + PDO MySQL, sem dependências externas.

> Substitua os placeholders: `SUA_SENHA_FORTE` (senha do banco) e, se for usar
> domínio depois, o `ServerName`.

> 🖥️ **Hosts RHEL/CentOS/AlmaLinux/cPanel:** o serviço web chama-se **`httpd`**
> (não `apache2`) e o projeto costuma ficar em **`~/public_html`** (ex.:
> `/home/algorithmia/public_html`). Nesses hosts, troque os comandos:
> - reiniciar/limpar OPcache: `sudo systemctl restart httpd` (ou o botão de
>   restart do painel/cPanel) — `apache2`/`a2ensite`/`ufw` não existem lá.
> - rode os scripts PHP como o usuário do site (não `www-data`), passando as
>   credenciais inline: `DB_HOST=127.0.0.1 DB_NAME=... DB_USER=... DB_PASS=... php database/seed-banco-questoes.php`.

## 1. Instalar o ambiente

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y apache2 libapache2-mod-php php php-mysql php-mbstring \
                    mysql-server git unzip
sudo a2enmod rewrite
sudo systemctl enable --now apache2 mysql
```

## 2. Criar o banco e um usuário dedicado

```bash
sudo mysql <<'SQL'
CREATE DATABASE IF NOT EXISTS algorithmia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'algorithmia'@'localhost' IDENTIFIED BY 'SUA_SENHA_FORTE';
GRANT ALL PRIVILEGES ON algorithmia.* TO 'algorithmia'@'localhost';
FLUSH PRIVILEGES;
SQL
```

## 3. Clonar o código

```bash
sudo git clone https://github.com/Ssnowzx/Algorithmia.git /var/www/algorithmia
sudo chown -R www-data:www-data /var/www/algorithmia
```

## 4. Rodar a migração (cria tabelas + seeds)

```bash
cd /var/www/algorithmia
sudo -u www-data DB_HOST=127.0.0.1 DB_NAME=algorithmia \
     DB_USER=algorithmia DB_PASS='SUA_SENHA_FORTE' \
     php database/migrate.php
```

Você deve ver `✅ Banco 'algorithmia' pronto.` e as contagens das tabelas.

## 5. Configurar o VirtualHost do Apache

O app lê as credenciais de variáveis de ambiente. Com mod_php, passamos via
`SetEnv` no vhost (o `getenv()` do PHP as enxerga).

```bash
sudo tee /etc/apache2/sites-available/algorithmia.conf > /dev/null <<'CONF'
<VirtualHost *:80>
    ServerName _default_
    DocumentRoot /var/www/algorithmia

    SetEnv DB_HOST 127.0.0.1
    SetEnv DB_NAME algorithmia
    SetEnv DB_USER algorithmia
    SetEnv DB_PASS SUA_SENHA_FORTE

    <Directory /var/www/algorithmia>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # Bloqueia acesso direto a pastas sensíveis (defesa extra).
    <DirectoryMatch "/var/www/algorithmia/(config|app|database|tools|openspec|docs)">
        Require all denied
    </DirectoryMatch>

    ErrorLog  ${APACHE_LOG_DIR}/algorithmia-error.log
    CustomLog ${APACHE_LOG_DIR}/algorithmia-access.log combined
</VirtualHost>
CONF
```

> ⚠️ Edite o arquivo e troque `SUA_SENHA_FORTE` pela senha real antes de ativar.

Ative o site e desative o default:

```bash
sudo a2dissite 000-default.conf
sudo a2ensite algorithmia.conf
sudo apache2ctl configtest
sudo systemctl reload apache2
```

## 6. Liberar o firewall (se o UFW estiver ativo)

```bash
sudo ufw allow 'Apache'   # ou: sudo ufw allow 80/tcp
```

## 7. Testar

Abra `http://SEU_IP_DA_VPS/` no navegador. A home do Algorithmia deve carregar.

---

## Atualizar o jogo depois (deploy de novas versões)

A migração é **não-destrutiva** — preserva contas e progresso. **Não pule o passo do
`migrate.php`**: só o `git pull` atualiza o código, mas é o `migrate.php` que aplica
as migrações e **as perguntas novas** no banco (`seed-banco-questoes.php`, idempotente).

```bash
cd /var/www/algorithmia
sudo -u www-data git pull
sudo -u www-data DB_HOST=127.0.0.1 DB_NAME=algorithmia \
     DB_USER=algorithmia DB_PASS='SUA_SENHA_FORTE' \
     php database/migrate.php
sudo systemctl reload apache2
```

> ⚠️ **`--reset` é o ÚNICO modo destrutivo** (faz `DROP DATABASE` e apaga contas e
> progresso). O `migrate.php` sem flag **nunca** apaga dados.

### Atualizar só o banco de perguntas (sem mexer no resto)

Se quiser apenas garantir as perguntas no banco (ex.: o host foi montado importando
`schema.sql` + `seeds.sql`, que **não** incluem o banco de questões ampliado):

```bash
cd /var/www/algorithmia
sudo -u www-data DB_HOST=127.0.0.1 DB_NAME=algorithmia \
     DB_USER=algorithmia DB_PASS='SUA_SENHA_FORTE' \
     php database/seed-banco-questoes.php
```

Confira o resultado (cada fase de combate deve ter ~9–14 perguntas, não 4–7):

```bash
mysql -u algorithmia -p algorithmia \
  -e "SELECT fase_id, COUNT(*) FROM desafios GROUP BY fase_id;"
```

## Adicionar domínio + HTTPS depois

```bash
sudo apt install -y certbot python3-certbot-apache
# Troque ServerName _default_ por seu domínio no .conf, então:
sudo certbot --apache -d seu-dominio.com
```

## Troubleshooting

- **Erro de conexão com banco**: confira se os `SetEnv` batem com o usuário/senha
  criados no passo 2. Teste: `mysql -u algorithmia -p algorithmia`.
- **404 em todas as rotas**: o `mod_rewrite` não está ativo ou `AllowOverride All`
  faltando. Rode `sudo a2enmod rewrite && sudo systemctl restart apache2`.
- **Página em branco / erro 500**: veja `sudo tail -f /var/log/apache2/algorithmia-error.log`.
- **Imagens não aparecem**: confirme `sudo chown -R www-data:www-data /var/www/algorithmia`.
- **As perguntas se repetem / não são aleatórias**: o banco do host está sem o banco de
  questões ampliado (foi montado só com `schema.sql` + `seeds.sql`). Rode o
  `seed-banco-questoes.php` (ou `migrate.php`) — ver "Atualizar só o banco de perguntas".
  O sorteio (`BatalhaService::sortearDesafios`) embaralha o pool; com poucas perguntas
  por fase ele cai no atalho e devolve sempre as mesmas.
