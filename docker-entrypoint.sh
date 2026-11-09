#!/bin/sh
set -e

# Pastikan hanya satu MPM (prefork) yang aktif
rm -f /etc/apache2/mods-enabled/mpm_event.load /etc/apache2/mods-enabled/mpm_event.conf
rm -f /etc/apache2/mods-enabled/mpm_worker.load /etc/apache2/mods-enabled/mpm_worker.conf
a2enmod mpm_prefork >/dev/null 2>&1 || true

PORT="${PORT:-10000}"
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/\*:80/*:${PORT}/" /etc/apache2/sites-available/000-default.conf

php artisan config:clear
php artisan storage:link || true
php artisan migrate --force
php artisan db:seed --force

exec apache2-foreground