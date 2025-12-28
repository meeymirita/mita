#!/bin/bash
set -e

echo "🔄 Настройка Laravel приложения..."

# ========== ВЫБОР ПРАВИЛЬНОГО SUPERVISORD КОНФИГА ==========
if [ "$APP_ENV" = "production" ]; then
    if [ -f /var/www/html/docker/supervisord.prod.conf ]; then
        cp /var/www/html/docker/supervisord.prod.conf /etc/supervisor/conf.d/supervisord.conf
    else
        cp /var/www/html/docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
    fi
else
    cp /var/www/html/docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
fi

echo "✅ Supervisord конфиг  $APP_ENV"

mkdir -p /var/www/html/storage/framework/{sessions,views,cache}
mkdir -p /var/www/html/bootstrap/cache
mkdir -p /var/www/html/storage/logs

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

if [ ! -f /var/www/html/.env ]; then
    cp .env.example .env
    echo "Создан файл .env"
fi

while ! nc -z mysql 3306; do
  sleep 1
done

while ! nc -z redis 6379; do
  sleep 1
done

while ! nc -z rabbitmq 5672; do
  sleep 1
done

if [ ! -d /var/www/html/vendor ] || [ ! -f /var/www/html/vendor/autoload.php ]; then
    composer install --no-dev --optimize-autoloader
fi

php artisan storage:link
php artisan key:generate
php artisan migrate --force

# Запускаем Supervisord
exec "$@"
