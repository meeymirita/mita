package redis

import (
	"log"
	"os"
	"strconv"
	"time"

	"github.com/go-redis/redis"
)

func ExampleNewClient() *redis.Client {
	host := os.Getenv("REDIS_HOST")
	if host == "" {
		host = "localhost"
	}
	password := os.Getenv("REDIS_PASSWORD")
	if password == "" {
		password = "mirita_password"
	}
	db := 1
	if s := os.Getenv("REDIS_DB"); s != "" {
		if n, err := strconv.Atoi(s); err == nil {
			db = n
		}
	}
	client := redis.NewClient(&redis.Options{
		Addr:     host + ":6379",
		Password: password,
		DB:       db,
	})

	_, err := client.Ping().Result()
	if err != nil {
		log.Fatal(err)
	}
	return client
}

func Set(client *redis.Client, key string, value interface{}, expiration time.Duration) error {
	return client.Set(key, value, expiration).Err()
}

func Get(client *redis.Client, key string) (string, error) {
	return client.Get(key).Result()
}

func Close(client *redis.Client) error {
	return client.Close()
}
