<?php

namespace App\Listeners;

use App\Events\VerificationCodeMailEvent;
use App\Mail\VerificationCodeMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class VerificationCodeMailListener implements ShouldQueue
{
    public string $queue = 'emails_queue';
    /**
     * Количество попыток выполнения
     *
     * @var int
     */
    public int $tries = 3;

    public function __construct()
    {
    }

    /**
     * @param VerificationCodeMailEvent $event
     * @return void
     * @throws \Exception
     */
    public function handle(VerificationCodeMailEvent $event): void
    {
        \Log::info('пришло в листенер', [
            'user_id' => $event->user->id,
            'email' => $event->user->email,
            'code' => $event->code,
        ]);

        try {
            \Log::info('перед емаил');
            Mail::to($event->user->email)
                ->send(new VerificationCodeMail($event->user, $event->code));

            \Log::info('Email отправлен успешно', [
                'user_id' => $event->user->id,
                'email' => $event->user->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Ошибка отправки email', [
                'user_id' => $event->user->id,
                'email' => $event->user->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
        \Log::info('=== КОНЕЦ handle() ===');
    }
}
