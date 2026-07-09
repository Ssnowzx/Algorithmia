# Imagem de produção do Algorithmia.
#
# O contexto de build é a RAIZ do repositório, não `platform/`: o jogo em PHP puro
# e a arte vivem lá fora, e a imagem precisa das duas coisas.
#
# A arte (143 MB) NÃO entra na imagem — ela é montada em leitura no runtime. Assar
# 143 MB imutáveis em cada release tornaria o deploy lento e o rollback caro, para
# guardar arquivos que quase nunca mudam.

# ---------------------------------------------------------------- dependências
FROM composer:2 AS dependencias

WORKDIR /app
COPY platform/composer.json platform/composer.lock ./

# --no-dev: PHPUnit, Pint e Larastan não vão para produção.
# --no-scripts: os scripts do Laravel precisam do código, que ainda não copiamos.
RUN composer install \
        --no-dev \
        --no-scripts \
        --no-autoloader \
        --prefer-dist \
        --no-interaction \
        --no-progress

# --------------------------------------------------------------------- runtime
FROM php:8.4-fpm-alpine AS runtime

# `install-php-extensions` resolve as bibliotecas de sistema de cada extensão.
#
# `pdo_mysql` não serve ao jogo — serve ao comando `algorithmia:importar`, que lê o
# banco legado uma única vez, no dia do corte. Sai da imagem quando o MySQL for
# aposentado.
COPY --from=mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_pgsql pdo_mysql opcache pcntl

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

COPY platform/docker/php/php.ini /usr/local/etc/php/conf.d/algorithmia.ini
COPY platform/docker/php/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

COPY --from=dependencias /app/vendor ./vendor
COPY platform/ ./

# `public/img` é um symlink no repositório (aponta para a arte, fora de platform/).
# Ele não pode ir para a imagem: o alvo não existe aqui, e o bind-mount do runtime
# precisa de um diretório de verdade.
RUN if [ -L public/img ]; then rm public/img; fi \
    && mkdir -p public/img storage/framework/cache storage/framework/views \
                storage/framework/sessions storage/logs bootstrap/cache

# O autoloader otimizado só pode ser gerado com o código presente.
RUN composer dump-autoload --no-dev --optimize --classmap-authoritative \
    && chown -R www-data:www-data storage bootstrap/cache

# `php artisan optimize` (config/route/view cache) roda no entrypoint, não aqui:
# ele lê o .env, que só existe no host.

USER www-data
EXPOSE 9000
ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]
