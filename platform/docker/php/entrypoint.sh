#!/usr/bin/env sh
set -eu

cd /var/www/platform

mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache

exec docker-php-entrypoint "$@"
