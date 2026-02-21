package queue

import amqp "github.com/rabbitmq/amqp091-go"

func ConnectRabbit() (*amqp.Connection, *amqp.Channel, error) {
	conn, err := amqp.Dial("amqp://mirita_user:mirita_password@rabbitmq:5672/")
	if err != nil {
		return nil, nil, err
	}

	ch, err := conn.Channel()
	if err != nil {
		return nil, nil, err
	}

	return conn, ch, nil
}
