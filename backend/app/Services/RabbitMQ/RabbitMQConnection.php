<?php 

namespace App\Services\RabbitMQ;

use PhpAmqpLib\Connection\AMQPStreamConnection;
class RabbitMQConnection
{
    // хост rabbitmq
    private $host;
    // порт rabbitmq
    private $port;
    // пользователь rabbitmq
    private $user;
    // пароль rabbitmq
    private $password;
    // vhost rabbitmq
    private $vhost;

    public function __construct()
    {
        $this->host = config('rabbitmq.connections.rabbitmq.host');
        $this->port = config('rabbitmq.connections.rabbitmq.port');
        $this->user = config('rabbitmq.connections.rabbitmq.user');
        $this->password = config('rabbitmq.connections.rabbitmq.password');
        $this->vhost = config('rabbitmq.connections.rabbitmq.vhost', '/');
    }

    public function createConnection(): AMQPStreamConnection  
    {
        return new AMQPStreamConnection(
            host: $this->host,
            port: (int) $this->port,
            user: $this->user,
            password: $this->password,
            vhost: $this->vhost
        );
    }
}