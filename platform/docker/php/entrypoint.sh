#!/usr/bin/env sh
set -eu

cd /var/www/platform

if [ ! -f vendor/autoload.php ]; then
  composer install --no-interaction --prefer-dist --no-progress
fi

exec docker-php-entrypoint "$@"
