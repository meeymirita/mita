# https://pkg.go.dev/github.com/air-verse/air#section-readme
FROM dockerhub.timeweb.cloud/library/golang:1.25-alpine
WORKDIR /app
# Установка air в PATH
RUN go install github.com/air-verse/air@latest
COPY . .
# air, хот релоад (команда сборки через CLI — обход бага с парсингом cmd в toml)
CMD ["air", "--build.cmd=go build -o /app/tmp/main ./cmd/send-code", "--build.entrypoint=/app/tmp/main"]
