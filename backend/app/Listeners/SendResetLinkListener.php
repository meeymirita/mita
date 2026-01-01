<?php

namespace App\Listeners;

use App\Events\SendResetLinkEvent;
use App\Mail\SendMailreset;
use App\Mail\VerificationCodeMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendResetLinkListener implements ShouldQueue
{
    use InteractsWithQueue;
    /**
     *
     * @var string
     */
    public $connection = 'rabbitmq';
    /**
     * Имя очереди для обработки задания
     *
     * @var string
     */
    public string $queue = 'emails_queue';
    /**
     * Количество попыток выполнения задания
     *
     * @var int
     */
    public int $tries = 3;
    /**
     * Время, через которое можно повторить задание при ошибке
     *
     * @var int
     */
    public int $backoff = 10;
    /**
     * Handle the event.
     */
    public function handle(SendResetLinkEvent $event): void
    {
        Log::info('пришло в SendResetLinkEvent', [
            'user_id' => $event->user->id,
            'email' => $event->user->email,
        ]);

        try {
            if (empty($event->user->email)) {
                throw new \Exception('Email пользователя пустой');
            }

            Mail::to($event->user->email)
                ->send(new SendMailreset($event->user, $event->token));

            Log::info('ушло из SendResetLinkEvent', [
                'user_id' => $event->user->id,
                'email' => $event->user->email,
            ]);

            if ($this->job) {
                $this->job->delete();
            }

        } catch (\Exception $e) {
            Log::error('Ошибка отправки email SendResetLinkListener', [
                'user_id' => $event->user->id,
                'email' => $event->user->email,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
            ]);

            if ($this->job && $this->attempts() < $this->tries) {
                $this->job->release($this->backoff);
            }

            throw $e;
        }
    }
}
