<?php

namespace App\Listeners;

use App\Events\VerificationCodeMailEvent;
use App\Mail\VerificationCodeMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class VerificationCodeMailListener implements ShouldQueue
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
    public int $backoff = 60;

    /**
     * Обработка события
     *
     * @param VerificationCodeMailEvent $event
     * @return void
     * @throws \Exception
     */
    public function handle(VerificationCodeMailEvent $event): void
    {
        sleep(25);

        Log::info('VerificationCodeMailEvent обработка', [
            'user_id' => $event->user->id,
            'email' => $event->user->email,
            'code' => $event->code,
        ]);

        try {
            Mail::to($event->user->email)
                ->send(new VerificationCodeMail($event->user, $event->code));

            Log::info('Email отправлен успешно', [
                'user_id' => $event->user->id,
                'email' => $event->user->email,
            ]);
            if ($this->job) {
                // Ручное подтверждение успешной обработки
                $this->job->delete();
            }

        } catch (\Exception $e) {
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

    /**
     * Обработка неудачного задания
     *
     * @param VerificationCodeMailEvent $event
     * @param \Throwable $exception
     * @return void
     */
    public function failed(VerificationCodeMailEvent $event, \Throwable $exception): void
    {
        Log::critical('Задание провалено', [
            'user_id' => $event->user->id,
            'email' => $event->user->email,
            'attempts' => $this->attempts(),
            'exception' => $exception->getMessage(),
        ]);
    }
}
