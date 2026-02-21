package main

import (
	"encoding/json"
	"log"

	"auth-service/internal/email"
	"auth-service/internal/models"
	"auth-service/internal/queue"
)

const queueName = "reset_password_queue"

func main() {
	conn, ch, err := queue.ConnectRabbit()
	if err != nil {
		log.Fatal("RabbitMQ connect:", err)
	}
	defer conn.Close()
	defer ch.Close()

	q, err := ch.QueueDeclare(queueName, true, false, false, false, nil)
	if err != nil {
		log.Fatal("QueueDeclare:", err)
	}

	if err := ch.Qos(1, 0, false); err != nil {
		log.Fatal("Qos:", err)
	}
	log.Println("Очередь", q.Name)
	msgs, err := ch.Consume(q.Name, "", false, false, false, false, nil)
	if err != nil {
		log.Fatal("Consume:", err)
	}

	for d := range msgs {
		var msg models.ResetPasswordMessage
		if err := json.Unmarshal(d.Body, &msg); err != nil {
			log.Println(err)
			d.Nack(false, false)
			continue
		}

		if err := email.SendEmail(msg.Email, "", msg.Subject, msg.HTML); err != nil {
			log.Println(err)
			d.Nack(false, true)
			continue
		}

		d.Ack(false)
		log.Println("reset link sent →", msg.Email)
	}
}
