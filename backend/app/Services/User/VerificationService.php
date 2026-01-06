<?php

namespace App\Services\User;

use App\Events\VerificationCodeMailEvent;
use App\Models\User;
use Carbon\Carbon;
use Random\RandomException;

class VerificationService
{

    private const CODE_LIFETIME_MINUTES = 5;
    private const RESEND_TIMEOUT_MINUTES = 1;

    /**
     * @throws RandomException
     */
    public function sendVerificationCode(User $user): bool
    {
        // генерация кода
        $code = $user->generateVerificationCode();

        try {
            VerificationCodeMailEvent::dispatch($user, $code);
            \Log::info('code sent', ['user_id' => $user->id]);
            return true;
        } catch (\Exception $exception) {
            \Log::error('Failed to send verification code', [
                'user_id' => $user->id,
                'error' => $exception->getMessage()
            ]);

            $user->update([
                'verification_code' => null,
                'verification_code_expires_at' => null,
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
        if (!$user->verification_code_expires_at) {
            return $this->sendVerificationCode($user);
        }

        $codeSentAt = Carbon::parse($user->verification_code_expires_at)
            ->subMinutes(self::CODE_LIFETIME_MINUTES);

        // Время когда можно отправить повторно (время отправки + 1 минута)
        $canResendAt = $codeSentAt->addMinutes(self::RESEND_TIMEOUT_MINUTES);

        // Если сейчас время меньше времени повторной отправки
        if (now()->lessThan($canResendAt)) {
            $secondsLeft = now()->diffInSeconds($canResendAt);
            $minutesLeft = ceil($secondsLeft / 60);

            throw new \Exception('Повторный код можно запросить через ' . $minutesLeft . ' минут(ы)');
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
