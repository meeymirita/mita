# https://pkg.go.dev/github.com/air-verse/air#section-readme
FROM dockerhub.timeweb.cloud/library/golang:1.26-alpine
RUN apk add --no-cache git bash curl
# Установка air в PATH
RUN go install github.com/air-verse/air@latest
ENV PATH=$PATH:/root/go/bin
WORKDIR /app
COPY . .
EXPOSE 8080
# air, хот релоад
CMD ["air", "-c", "air.toml"]
