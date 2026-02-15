<?php

namespace App\Rabbit;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitPublisher
{
    public function sendVerification($user, $code)
    {
        $connection = new AMQPStreamConnection(
            'rabbitmq',
            5672,
            'mirita_user',
            'mirita_password'
        );
        $channel = $connection->channel();

        $channel->queue_declare('emails_queue', false, true, false, false);


        $payload = json_encode([
            'email' => $user->email,
            'code' => $code,
            'user_id' => $user->id
        ]);

        $msg = new AMQPMessage($payload);
        $channel->basic_publish($msg, '', 'emails_queue');

        $channel->close();
        $connection->close();
    }
}
