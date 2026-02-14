
1️⃣ Создаём папку сервиса
cd /var/www/mita/go-services
mkdir service-two
cd service-two

2️⃣ Инициализация Go модуля 
go mod init service-two

Создаёт файл go.mod, чтобы Go понимал зависимости сервиса.

3️⃣ Создаём минимальный main.go
touch main.go

Пример содержимого main.go:

package main

import (
"fmt"
"net/http"
)

func main() {
http.HandleFunc("/", func(w http.ResponseWriter, r *http.Request) {
fmt.Fprintln(w, "Hello from service-two!")
})
fmt.Println("Server running on :8080")
http.ListenAndServe(":8080", nil)
}

4️⃣ Подтягиваем зависимости
go mod tidy

5️⃣ Проверяем локальный запуск
go run main.go

6️⃣ Создаём air.toml для live reload
touch air.toml

Пример содержимого:

# .air.toml
root = "."
tmp_dir = "tmp"

[build]
cmd = "go build -o service-two ."
bin = "service-two"
include_ext = ["go", "tpl", "tmpl", "html"]

[color]
main = "yellow"

7️⃣ Dockerfile для dev с Air
FROM dockerhub.timeweb.cloud/library/golang:1.26-alpine

RUN apk add --no-cache git bash curl
RUN go install github.com/air-verse/air@latest

WORKDIR /app
COPY . .

EXPOSE 8080

CMD ["air", "-c", ".air.toml"]

8️⃣ docker-compose.yml для сервиса
service_two:
build:
context: ./go-services/service-two
dockerfile: Dockerfile
container_name: service_two
ports:
- "8083:8080"
volumes:
- ./go-services/service-two:/app
networks:
- app-network


💡 Итого:

go mod init — один раз на сервис

go mod tidy — после добавления зависимостей

go build — для сборки бинарника (prod)

air — для live reload (dev)

