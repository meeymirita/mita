<?php

namespace App\Services\RabbitMQ;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| При создании сервиса автоматически устанавливается соединение с RabbitMQ
| Создается "канал" для обмена сообщениями
| Соединение живет до уничтожения объекта
|--------------------------------------------------------------------------
*/

class RabbitMQService
{
    /**
     * @var $connection
     */
    protected $connection;
    /**
     * @var $channel
     */
    protected $channel;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        // автоподключение при создании
        $this->connect();
    }

    // подключение

    /**
     * @return void
     * @throws \Exception
     */
    protected function connect()
    {
        try {
            $config = config('rabbitmq.connections.default');

            $this->connection = new AMQPStreamConnection(
                $config['host'],
                $config['port'],
                $config['user'],
                $config['password'],
                $config['vhost']
            );

            $this->channel = $this->connection->channel();

            Log::info('✅ RabbitMQ connected successfully');

        } catch (\Exception $e) {
            Log::error('❌ RabbitMQ connection failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Специальные методы для постов
     */
    public function publishPostCreated(array $postData)
    {
        return $this->publish('post_created', $postData, 'post_created');
    }

    /**
     * @param array $postData
     * @return bool
     */
    public function publishPostUpdated(array $postData)
    {
        return $this->publish('post_updated', $postData, 'post_updated');
    }

    /**
     * @param array $postData
     * @return bool
     */
    public function publishPostDeleted(array $postData)
    {
        return $this->publish('post_deleted', $postData, 'post_deleted');
    }

    /*
    |--------------------------------------------------------------------------
    | $message формирование сообщения
    |   action - Тип события
    |   data - Данные
    |   timestamp - Время
    |   message_id - Уникальный ID
    |
    | Получаем имя очереди из конфига
    |   $queueName = config("rabbitmq.queues.{$queue}", $queue);
    |   $queueName = 'post_created' → 'post_created'
    |
    | Создание очереди (если не существует)
    |    $this->channel->queue_declare($queueName, false, true, false, false);
    |                                              /passive/durable/exclusive/auto_delete/
    |
    | Создание сообщения
    |   $amqpMessage = new AMQPMessage(json_encode($message));
    |
    | Отправка в очередь
    |    $this->channel->basic_publish($amqpMessage, '', $queueName);
    |
    |
    |
    |
    |
    |
    |--------------------------------------------------------------------------
    */
    /**
     * @param string $queue
     * @param array $data
     * @param string|null $action
     * @return bool
     */
    public function publish(string $queue, array $data, string $action = null)
    {
        try {
            $message = [
                'action' => $action,
                'data' => $data,
                'timestamp' => now(),
                'message_id' => uniqid('msg_', true)
            ];

            $queueName = config("rabbitmq.queues.{$queue}", $queue);

            $this->channel->queue_declare($queueName, false, true, false, false);

            $amqpMessage = new AMQPMessage(json_encode($message));

            $this->channel->basic_publish($amqpMessage, '', $queueName);

            Log::info("📨 Message published to RabbitMQ");

            return true;

        } catch (\Exception $e) {
            Log::error("❌ Failed to publish message to RabbitMQ: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Закрытие соединения Автоудаление после вызова
     */
    public function __destruct()
    {
        try {
            if ($this->channel && $this->channel->is_open()) {
                $this->channel->close();
            }
            if ($this->connection && $this->connection->isConnected()) {
                $this->connection->close();
            }
        } catch (\Exception $e) {
            Log::error('Error closing RabbitMQ connection: ' . $e->getMessage());
        }
    }
}
