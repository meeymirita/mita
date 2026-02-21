package main

import (
    "encoding/json"
    "fmt"
    "log"

    "auth-service/internal/email"
    "auth-service/internal/models"
    "auth-service/internal/queue"
)

func main() {

	conn, ch, err := queue.ConnectRabbit()
	if err != nil {
		log.Fatal(err)
	}
	defer conn.Close()
	defer ch.Close()

	q, err := ch.QueueDeclare("emails_queue", true, false, false, false, nil)
	if err != nil {
		log.Fatal(err)
	}
  

	msgs, err := ch.Consume(q.Name, "", false, false, false, false, nil)
	if err != nil {
		log.Fatal(err)
	}


	fmt.Println("Waiting messages...")

	for d := range msgs {

		var msg models.VerificationMessage

		if err := json.Unmarshal(d.Body, &msg); err != nil {
			log.Println(err)
			d.Nack(false, false)
			continue
		}

		if err := email.SendEmail(msg.Email, msg.Code); err != nil {
			log.Println(err)
			d.Nack(false, true)
			continue
		}

		d.Ack(false) 

		fmt.Println("sent →", msg.Email)
	}
}
