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


- бан и не даёт скачал или что то другое лечится зеркалом 
- dockerhub.timeweb.cloud/library/{image}
#### => ERROR [internal] load metadata for docker.io/library/php:8.4-fpm                                                                                                  30.8s
#### => ERROR [internal] load metadata for docker.io/library/composer:2.8  



#### 1
sudo apt update
sudo apt upgrade -y
#### 2
sudo apt install -y nodejs npm
#### 3
node --version
npm --version
#### 4
npm cache clean --force
#### 5
cd /var/www/mita/frontend
rm -rf node_modules package-lock.json
npm install
#### 6 nvm 
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash
#### 7
export NVM_DIR="$HOME/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"
[ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion"

#### nvm работает
nvm --version  # Должно показать версию, например 0.39.0

#### 8  Node.js
nvm use 24
#  версию по умолчанию 
nvm alias default 24
#  версии
node --version 
npm --version  
#  в папку проекта
cd /var/www/mita/frontend
#  старые зависимости
rm -rf node_modules package-lock.json
# npm install
docker-compose build vuejs
docker-compose up -d vuejs