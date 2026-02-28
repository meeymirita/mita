<?php

namespace App\Rabbit\User;

use PhpAmqpLib\Message\AMQPMessage;
use App\Services\RabbitMQ\RabbitMQConnection;

class SendUserCodeRabbitPublisher
{

    public $rabbitMQConnection;
    
    public function __construct(
        RabbitMQConnection $rabbitMQConnection
    )
    {
        $this->rabbitMQConnection = $rabbitMQConnection;
    }


    public function sendVerification($user)
    {
        $queueName = config('rabbitmq.queues.email', 'emails_queue');

        $connection = $this->rabbitMQConnection->createConnection();
        try {
            $channel = $connection->channel();
            $channel->queue_declare($queueName, false, true, false, false);
            
            $payload = json_encode([
                'email' => $user->email,
                'user_id' => $user->id,
                'type' => 'email_verification',
            ]);
            $channel->basic_publish(new AMQPMessage($payload), '', $queueName);
        } finally {
            if (isset($channel)) {
                $channel->close();
            }
            $connection->close();
        }
    }
}
