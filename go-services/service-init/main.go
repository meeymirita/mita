package main

import (
	"encoding/json"
	"fmt"
	"log"
	"net/smtp"

	amqp "github.com/rabbitmq/amqp091-go"
)

type VerificationMessage struct {
	Email  string `json:"email"`
	Code   string `json:"code"`
	UserID int    `json:"user_id"`
}

func sendEmail(to string, code string) error {

	from := "noreply@meeymirita.ru"
	smtpHost := "mailpit"
	smtpPort := "1025"

	message := []byte(
		"Subject: Verification Code\r\n\r\n" +
			"Your verification code: " + code,
	)

	return smtp.SendMail(
		smtpHost+":"+smtpPort,
		nil,
		from,
		[]string{to},
		message,
	)
}


func main() {

	conn, err := amqp.Dial("amqp://mirita_user:mirita_password@rabbitmq:5672/")
	if err != nil {
		log.Fatal(err)
	}
	defer conn.Close()

	ch, err := conn.Channel()
	if err != nil {
		log.Fatal(err)
	}
	defer ch.Close()

	q, err := ch.QueueDeclare(
		"emails_queue",
		true,
		false,
		false,
		false,
		nil,
	)
	if err != nil {
		log.Fatal(err)
	}

	msgs, err := ch.Consume(
		q.Name,
		"",
		false,
		false,
		false,
		false,
		nil,
	)
	if err != nil {
		log.Fatal(err)
	}

	fmt.Println("Waiting for messages...")

	forever := make(chan bool)

	go func() {
		for d := range msgs {

			var msg VerificationMessage

			err := json.Unmarshal(d.Body, &msg)
			if err != nil {
				log.Println("JSON parse error:", err)
				d.Nack(false, false)
				continue
			}

			fmt.Println("Email:", msg.Email)
			fmt.Println("Code:", msg.Code)

			// отправка письма
			err = sendEmail(msg.Email, msg.Code)
			if err != nil {
				log.Println("Email send error:", err)

				d.Nack(false, true)
				continue
			}

			d.Ack(false)

			fmt.Println("Email sent!")
			fmt.Println("--------------")
		}
	}()

	<-forever
}
