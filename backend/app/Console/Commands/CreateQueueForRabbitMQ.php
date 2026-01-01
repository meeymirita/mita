<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use PhpAmqpLib\Connection\AMQPStreamConnection;


class CreateQueueForRabbitMQ extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mirita:queue-for-rabbitmq';

    /**
     *
     * @var string
     */
    protected $description = 'Для докера скрипт создания очередей чтоб создавал при создании';

    /**
     * @return void
     * @throws \Exception
     * Документация http://localhost:15672/api/index.html
     */
    public function handle()
    {
        $config = config('queue.connections.rabbitmq.hosts.0');
        if (!$config) {
            \Log::error('Конфига нету');
            return;
        }

        $host = $config['host'];
        $port = $config['port'];
        $login = $config['user'];
        $password = $config['password'];

        try {
            $response = Http::timeout(10)
                ->withBasicAuth($login, $password)
                ->get("http://{$host}:15672/api/queues/%2F");
            $existingQueues = [];
            if ($response->successful()) {
                $queue = $response->json();
                foreach ($queue as $queueItem) {
                    $existingQueues[] = $queueItem['name'];
                }
            } else {
                $this->warn($response->status());
            }

            $connection = new AMQPStreamConnection($host, $port, $login, $password);
            $channel = $connection->channel();

            $queuesToCreate = [
                'laravel_queue',
                'emails_queue',
            ];
            $createdQueues = [];
            foreach ($queuesToCreate as $queueName) {
                if (in_array($queueName, $existingQueues)) {
                    $this->line("Очередь существует - '{$queueName}'");
                    continue;
                }
                try {
                    $channel->queue_declare($queueName, false, true, false, false);

                    $createdQueues[] = $queueName;
                    $this->info("Очередь '{$queueName}' создана");

                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            try {
                $channel->exchange_declare('laravel_exchange', 'direct', false, true, false);
                $this->info("Exchange 'laravel_exchange' создан");

                foreach ($createdQueues as $queueName) {
                    $channel->queue_bind($queueName, 'laravel_exchange', $queueName . '_key');
                }

            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }

            $channel->close();
            $connection->close();
            \Log::info('созданы или обновлены очереди');
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
        }
    }
}
