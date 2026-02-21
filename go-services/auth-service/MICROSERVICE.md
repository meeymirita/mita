# auth-service — описание микросервиса

Микросервис для отправки писем с кодом верификации. Работает как потребитель очереди RabbitMQ: получает сообщения с email и кодом, отправляет письмо через SMTP (в текущей конфигурации — Mailpit).

---

## Назначение

- **Вход:** сообщения в очереди RabbitMQ `emails_queue` в формате JSON: `email`, `code`, `user_id`.
- **Действие:** отправка письма с темой «Code» и телом «Your code: &lt;code&gt;» на указанный адрес.
- **Выход:** письмо доставляется через SMTP; при ошибке парсинга или отправки сообщение отклоняется (Nack) с повторной постановкой в очередь при ошибке отправки.

Сервис не предоставляет HTTP API и не слушает порты; он только читает очередь и шлёт почту.

---

## Архитектура и окружение

- **Язык:** Go.
- **Очередь:** RabbitMQ (очередь `emails_queue`, durable).
- **Почта:** SMTP (в docker-окружении — Mailpit на `mailpit:1025`).
- **Сборка и запуск:** Docker, внутри контейнера — Air для hot-reload при разработке.

Зависимости в `docker-compose`: RabbitMQ и Mailpit (или другой SMTP-сервер) должны быть доступны в одной сети с контейнером `auth-service`.

---

## Структура проекта

```text
auth-service/
├── cmd/
│   └── send-code/
│       └── main.go          # точка входа: подключение к RabbitMQ, потребление очереди, вызов отправки
├── internal/
│   ├── email/
│   │   └── email.go         # отправка письма по SMTP
│   ├── models/
│   │   └── models.go       # структура VerificationMessage (email, code, user_id)
│   └── queue/
│       └── rabbit.go       # подключение к RabbitMQ
├── tmp/                     # артефакты air (бинарник, логи)
├── air.toml                 # конфиг Air (hot-reload)
├── build.sh                 # скрипт ручной сборки
├── Dockerfile
├── go.mod
├── go.sum
├── README.md                # настройка, go.mod/go.sum, build.sh, air.toml
└── MICROSERVICE.md         # этот файл
```

---

## Поток данных

1. **Старт.** Подключение к RabbitMQ по `amqp://mirita_user:mirita_password@rabbitmq:5672/`, объявление очереди `emails_queue`, подписка на потребление.
2. **Цикл.** Для каждого сообщения:
   - Парсинг JSON в `VerificationMessage` (email, code, user_id).
   - При ошибке парсинга — `Nack(false, false)` (не requeue), логирование, переход к следующему сообщению.
   - Вызов `email.SendEmail(msg.Email, msg.Code)` (SMTP в Mailpit).
   - При ошибке отправки — `Nack(false, true)` (requeue), логирование.
   - При успехе — `Ack(false)`, вывод в лог «sent → &lt;email&gt;».
3. Сервис работает до остановки контейнера.

---

## Внутренние компоненты

### cmd/send-code/main.go

- Устанавливает соединение с RabbitMQ через `queue.ConnectRabbit()`.
- Объявляет очередь `emails_queue` (durable).
- Запускает потребление, в бесконечном цикле читает сообщения, десериализует в `models.VerificationMessage`, вызывает `email.SendEmail`, делает Ack/Nack.

### internal/queue/rabbit.go

- Функция `ConnectRabbit()`: подключается к RabbitMQ по фиксированному URL, создаёт канал, возвращает `*amqp.Connection`, `*amqp.Channel`, `error`.
- Зависимость: `github.com/rabbitmq/amqp091-go`.

### internal/models/models.go

- Структура `VerificationMessage` с полями `Email`, `Code`, `UserID` и JSON-тегами для десериализации из тела сообщения.

### internal/email/email.go

- Функция `SendEmail(to, code string)`: формирует простое письмо (Subject: Code, тело «Your code: …») и отправляет через `smtp.SendMail` на хост `mailpit`, порт `1025`, от `noreply@meeymirita.ru`.

---

## Конфигурация (жёстко в коде)

Сейчас параметры зашиты в коде; для продакшена их лучше вынести в переменные окружения или конфиг.

- **RabbitMQ:** `amqp://mirita_user:mirita_password@rabbitmq:5672/`
- **SMTP:** хост `mailpit`, порт `1025`, отправитель `noreply@meeymirita.ru`
- **Очередь:** имя `emails_queue`, durable

---

## Запуск и отладка

- **Через Docker:** `docker compose up -d auth-service` (после `docker compose build auth-service` при необходимости). Логи: `docker compose logs -f auth-service`.
- **Ожидание в логах:** после успешной сборки и старта — строка `Waiting messages...`; при отправке — `sent → &lt;email&gt;`.
- **Ручная сборка в контейнере:** `docker exec -it auth-service sh /app/build.sh` (или выполнить команду из `build.sh` вручную).

---

## Зависимости модуля (go.mod)

- **Go:** 1.22.
- **Внешний пакет:** `github.com/rabbitmq/amqp091-go v1.10.0` (клиент AMQP 0-9-1 для RabbitMQ).

Остальное — стандартная библиотека (`encoding/json`, `fmt`, `log`, `net/smtp`).

---

## Связь с остальной системой

Сервис не знает, кто кладёт сообщения в `emails_queue`. Обычно это делает бэкенд (например, Laravel): после генерации кода верификации он публикует в очередь JSON `{ "email", "code", "user_id" }`, а auth-service только потребляет очередь и шлёт письмо. Так отправка почты отделена от основного приложения и может масштабироваться или перезапускаться независимо.
