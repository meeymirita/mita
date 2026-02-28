package main

import (
	"fmt"
	"log"
	"os"

	"github.com/go-redis/redis"
)

func main() {
	// password := os.Getenv("REDIS_PASSWORD")
	// log.Println("password →", password)
	// host := os.Getenv("REDIS_HOST")
	// log.Println("host →", host)
	ExampleNewClient()
}

func ExampleNewClient() {
	host := os.Getenv("REDIS_HOST")
	log.Println("host →", host)
	if host == "" {
		host = "localhost"
	}
	password := os.Getenv("REDIS_PASSWORD")
	if password == "" {
		password = "mirita_password"
	}

	client := redis.NewClient(&redis.Options{
		Addr:     host + ":6379",
		Password: password,
		DB:       0,
	})

	pong, err := client.Ping().Result()
	if err != nil {
		log.Fatal(err)
	}
	fmt.Println(pong)
}