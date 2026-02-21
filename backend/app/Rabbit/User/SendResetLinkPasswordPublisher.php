<?php
namespace App\Rabbit\User;

use Illuminate\Support\Facades\View;
use PhpAmqpLib\Message\AMQPMessage;
use App\Services\RabbitMQ\RabbitMQConnection;

class SendResetLinkPasswordPublisher
{
    public $rabbitMQConnection;
    // url изображений для email
    private $sakuraUrl;
    // url изображений для email
    private $himaryUrl;
    // url frontend
    private $frontendUrl;
    public function __construct(
        RabbitMQConnection $rabbitMQConnection
    )
    {
        $this->rabbitMQConnection = $rabbitMQConnection;
        $this->frontendUrl = config('app.url');
        $this->sakuraUrl = config('mail.verification.sakura_url');
        $this->himaryUrl = config('mail.verification.himary_url');
    }

    public function sendResetLink($user,$token)
    {
        $queueName = config('rabbitmq.queues.reset_password', 'reset_password_queue');
        $connection = $this->rabbitMQConnection->createConnection();
        try{
            
            $channel = $connection->channel();
            $channel->queue_declare($queueName, false, true, false, false);

            $html = View::make('emails.password_reset', [
                'sakura_url' => $this->frontendUrl . $this->sakuraUrl,
                'himary_url' => $this->frontendUrl . $this->himaryUrl,
                'token' => $token,
                'user' => $user,
                'frontend_url' => rtrim($this->frontendUrl, '/'),
            ])->render();
            

            $payload = json_encode([
                'email' => $user->email,
                'token' => $token,
                'user_id' => $user->id,
                'subject' => 'Ссылка на сброс пароля',
                'html' => $html,
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