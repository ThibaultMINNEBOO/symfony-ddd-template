#!/bin/bash
sleep 5

# Set TimeZone for docker image and php
ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
echo "date.timezone = '$TZ'" > "$PHP_INI_DIR/php.ini"

composer install

php bin/console c:c
php bin/console d:m:m --no-interaction
php bin/console d:f:l --no-interaction
php bin/console importmap:install
php bin/console tailwind:build

# Start frankenphp

frankenphp run --config /app/docker/Caddyfile &

# Start supervisord
/usr/bin/supervisord &

# Wait for any process to exit
wait -n

# Exit with status of process that exited first
exit $?
