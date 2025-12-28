Документация по развертыванию проекта (Laravel + Nuxt)

✅ Картинки из Laravel: https://meeymirita.ru/storage/posts/IXIIVwWIJFGF1nGf2vNuaxH9HDn61RhyEvS3zT00.jpg → 200 OK
✅ Главная страница: https://meeymirita.ru/ → 200 OK (от Nuxt)
🚀 Быстрый запуск
1. Клонирование и настройка
   bash
   git clone _<ваш-репозиторий>
   cd mirita
   cp [development.env](development.env) .env_
# Заполните .env файл (DB, APP_KEY и др.)
2. Запуск в продакшн
   bash
# Сборка и запуск
docker-compose -f docker-compose.prod.yml up -d --build
docker-compose -f docker-compose.prod.yml exec laravel php artisan storage:link --force
# Проверка статуса
docker-compose -f docker-compose.prod.yml ps

# Просмотр логов
docker-compose -f docker-compose.prod.yml logs -f
📁 Структура проекта
text
mirita/
├── backend/          # Laravel (API)
│   ├── public/      # Статические файлы Laravel
│   └── storage/     # Загружаемые файлы (через /storage/)
├── frontend/        # Nuxt.js (SSR фронтенд)
└── docker/          # Docker конфигурации
🌐 Доступные URL
✅ Работающие пути:
Главная страница: https://meeymirita.ru/

API Laravel: https://meeymirita.ru/api/*

Статика Nuxt: https://meeymirita.ru/_nuxt/*

Favicon: https://meeymirita.ru/favicon.ico (из Nuxt)

🖼️ Картинки:
Из Laravel public: https://meeymirita.ru/public/имя_файла.jpg

bash
# Положить картинку в:
backend/public/имя_файла.jpg
Из Laravel storage: https://meeymirita.ru/storage/имя_файла.jpg

bash
# Положить картинку в:
backend/storage/app/public/имя_файла.jpg

# Или из контейнера:
docker exec laravel_prod cp /path/to/image.jpg /var/www/html/storage/app/public/
🔧 Основные команды
Docker команды:
bash
# Перезапуск сервисов
docker-compose -f docker-compose.prod.yml restart [service]

# Остановка
docker-compose -f docker-compose.prod.yml stop

# Запуск
docker-compose -f docker-compose.prod.yml start

# Просмотр логов
docker-compose -f docker-compose.prod.yml logs -f nginx
docker-compose -f docker-compose.prod.yml logs -f laravel
docker-compose -f docker-compose.prod.yml logs -f nuxtjs
Вход в контейнеры:
bash
# Laravel
docker exec -it laravel_prod bash

# Nginx
docker exec -it nginx_prod sh

# Nuxt
docker exec -it nuxtjs_prod bash
🛠️ Устранение проблем
1. Статика Nuxt не грузится (404)
   bash
# Проверка файлов
docker exec nginx_prod ls -la /var/www/html/frontend/.output/public/_nuxt/

# Перезапуск Nginx
docker-compose -f docker-compose.prod.yml restart nginx
2. Картинки не отдаются
   bash
# Проверка пути
curl -I https://meeymirita.ru/public/test.jpg
curl -I https://meeymirita.ru/storage/test.jpg

# Создание тестовой картинки
docker exec laravel_prod bash -c "echo 'test' > /var/www/html/storage/app/public/test.jpg"
3. Laravel API не работает
   bash
# Проверка
curl https://meeymirita.ru/api/test

# Просмотр логов Laravel
docker logs laravel_prod --tail 50
📊 Проверка работоспособности
bash
#!/bin/bash
URL="https://meeymirita.ru"
echo "Проверка сайта $URL"

check() {
if curl -s -o /dev/null -w "%{http_code}" "$1" | grep -q "200\|301\|302"; then
echo "✅ $2"
else
echo "❌ $2"
fi
}

check "$URL/" "Главная страница"
check "$URL/_nuxt/entry.ZdVBaBXW.css" "CSS Nuxt"
check "$URL/favicon.ico" "Favicon"
check "$URL/public/me.jpg" "Картинка из public"
check "$URL/api/test" "API Laravel"
⚡ Быстрые фиксы
Обновить Nginx конфиг:
bash
# После изменений в docker/nginx/nginx.prod.conf
docker-compose -f docker-compose.prod.yml restart nginx
Пересобрать фронтенд:
bash
docker-compose -f docker-compose.prod.yml restart nuxtjs
Очистить кэш Laravel:
bash
docker exec laravel_prod php artisan optimize:clear
🔐 SSL сертификаты
Автоматически настроены через Let's Encrypt

Сертификаты монтируются из /etc/letsencrypt

HTTP → HTTPS редирект настроен

📈 Мониторинг
bash
# Статус контейнеров
docker-compose -f docker-compose.prod.yml ps

# Использование ресурсов
docker stats

# Логи в реальном времени
docker-compose -f docker-compose.prod.yml logs -f