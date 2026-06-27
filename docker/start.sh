#!/usr/bin/env bash
set -e

export PORT="${PORT:-10000}"
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/\${PORT}/${PORT}/g" /etc/apache2/sites-available/000-default.conf

mkdir -p storage/app/public storage/app/livewire-tmp storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
rm -f public/hot

php artisan storage:link || true
php artisan optimize:clear
php artisan migrate --force

if [ -n "${ADMIN_PASSWORD:-}" ]; then
    php artisan bse:create-admin \
        --name="${ADMIN_NAME:-BSE Admin}" \
        --username="${ADMIN_USERNAME:-admin}" \
        --email="${ADMIN_EMAIL:-admin@admin.com}" \
        --password="${ADMIN_PASSWORD}"
fi

php artisan config:cache
php artisan view:cache

exec apache2-foreground
