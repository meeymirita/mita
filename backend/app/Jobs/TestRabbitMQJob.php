<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TestRabbitMQJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $message;

    /**
     * @param $message
     */
    public function __construct($message)
    {
        $this->message = $message;
        $this->onQueue('laravel_queue');
    }

    /**
     * @return void
     */
    public function handle()
    {
        Log::info('RabbitMQ Job executed: ' . $this->message);
        echo "RabbitMQ Job: {$this->message}\n";
    }
}
