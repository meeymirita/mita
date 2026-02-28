package main

import (
	"crypto/rand"
	"encoding/json"
	"fmt"
	"io"
	"log"
	"time"
	"auth-service/internal/email"
	"auth-service/internal/models"
	"auth-service/internal/queue"
	"auth-service/internal/redis"
)

func main() {
	conn, ch, err := queue.ConnectRabbit()
	if err != nil {
		log.Fatal("RabbitMQ connect:", err)
	}
	defer conn.Close()
	defer ch.Close()

	redisClient := redis.ExampleNewClient()
	defer redis.Close(redisClient)

	q, err := ch.QueueDeclare("emails_queue", true, false, false, false, nil)
	if err != nil {
		log.Fatal("QueueDeclare:", err)
	}

	// Prefetch 1 — консьюмер готов получать сообщения (в Docker без этого иногда не стартует приём)
	if err := ch.Qos(1, 0, false); err != nil {
		log.Fatal("Qos:", err)
	}
	msgs, err := ch.Consume(q.Name, "", false, false, false, false, nil)
	if err != nil {
		log.Fatal("Consume:", err)
	}
	for d := range msgs {
	
		var msg models.VerificationMessage

		if err := json.Unmarshal(d.Body, &msg); err != nil {
			log.Println(err)
			d.Nack(false, false)
			continue
		}
  
		// Generate code
		code := EncodeToString(6)

		key := "verification_code:" + fmt.Sprintf("%d", msg.UserID)
		if err := redis.Set(redisClient, key, code, 1*time.Minute); err != nil {
			log.Fatal("Redis Set:", err)
		}
		val, err := redis.Get(redisClient, key)
		if err != nil {
			log.Fatal("Redis Get:", err)
		}
		log.Println("Redis get →", val)

		if err := email.SendEmail(msg.Email); err != nil {
			log.Println(err)
			d.Nack(false, true)
			continue 
		}

		d.Ack(false)

		log.Println("sent →", msg.Email)
	}
}

func EncodeToString(max int) string {
	b := make([]byte, max)
	n, err := io.ReadAtLeast(rand.Reader, b, max)
	if n != max {
		panic(err)
	}
	for i := 0; i < len(b); i++ {
		b[i] = table[int(b[i])%len(table)]
	}
	return string(b)
}
var table = [...]byte{'1', '2', '3', '4', '5', '6', '7', '8', '9', '0'}