<?php

namespace App\Services\User;

use App\Events\VerificationCodeMailEvent;
use App\Mail\VerificationCodeMail;
use App\Models\User;
use App\Traits\ColoredLogs;
use Illuminate\Support\Facades\Mail;
use Random\RandomException;

class VerificationService
{
    /**
     * @throws RandomException
     */
    public function sendVerificationCode(User $user): bool
    {
        // генерация кода
        $code = $user->generateVerificationCode();
        try {
            \Log::info('ушло в ивент');
            VerificationCodeMailEvent::dispatch($user, $code);
        } catch (\Exception $exception) {
            \Log::error('Failed to send verification code', [
                'user_id' => $user->id,
                'error' => $exception->getMessage()
            ]);
            return false;
        }

        return true;
    }

    /**
     * @param User $user
     * @return bool
     * @throws RandomException
     */
    public function resendVerificationCode(User $user): bool
    {
        if (
            $user->verification_code_expires_at &&
            $user->verification_code_expires_at->subMinutes(1) < now()
        ) {
            throw new \Exception('Повторный код можно запросить через 1 минуту');
        }
        return $this->sendVerificationCode($user);
    }


    /**
     * Подтверждение кода
     * @param User $user
     * @param string $code
     * @return bool
     */
    public function verifyCode(User $user, string $code): bool
    {
        if (!$user->verifyCode($code)) {
            return false;
        }

        $user->markEmailAsVerified();
        return true;
    }
}
