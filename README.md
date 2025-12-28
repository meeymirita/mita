# Docker + Vue + Laravel + RabbitMQ + Redis
| Сервис | URL                              | Назначение |
|--------|----------------------------------|------------|
|Grafana| http://localhost:3000|Красивая статистика и мониторинг|
|Laravel API| http://localhost:8080|API бэкенд для Vue.js приложения|
|Vue.js| http://localhost:5173|Фронтенд-приложение|
|phpMyAdmin| http://localhost:8081|Управление базами данных MySQL|
|Mailpit| http://localhost:8025|Просмотр отправленных писем|
|RabbitMQ| http://localhost:1567|Очереди сообщений|
|Redis| http://localhost:6379 |Кеширование данных|
|MySQL| http://localhost:3307 |База данных|
#### Скопировать файлик
- cp [.env.local](.env.local) .env
#### Запустить
docker compose up -d
#### Логи
- docker compose logs -f laravel
- docker compose logs -f nginx
- docker compose logs -f mysql

# Пересобрать контейнеры
- docker compose up -d --build





