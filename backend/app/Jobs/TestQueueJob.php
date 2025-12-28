<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class TestQueueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public $message;
    public $queue;

    /**
     * @param string $message
     * @param string $queue
     */
    public function __construct(string $message, string $queue = 'default')
    {
        $this->message = $message;
        $this->queue = $queue;

        $this->onConnection('rabbitmq');
        $this->onQueue($queue);
    }

    /**
     * @return void
     */
    public function handle()
    {
        Log::info("🎯 Test Queue Job Executed", [
            'message' => $this->message,
            'queue' => $this->queue,
            'timestamp' => now()->toISOString()
        ]);

        echo "Test Job: {$this->message} on queue: {$this->queue}\n";
    }
}
