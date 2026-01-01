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
        Log::error('email', [
            'user_id' => $event->user->id,
            'user' => $event->user,
            'token' => $event->token,
        ]);
        try {
            Mail::to($event->user->email)
                ->send(new SendMailreset($event->user, $event->token));
            if ($this->job) {
                // Ручное подтверждение успешной обработки
                $this->job->delete();
            }
        }catch (\Exception $e) {
            Log::error('Ошибка отправки email', [
                'user_id' => $event->user->id,
                'email' => $event->user->email,
                'error' => $e->getMessage(),
            ]);

            if ($this->job && $this->attempts() < $this->tries) {
                $this->job->release($this->backoff);
            }

            throw $e;
        }
    }
}
