<?php

namespace App\Services\User;

use App\Models\User;
use App\Rabbit\User\SendUserCodeRabbitPublisher;
use Carbon\Carbon;
use Random\RandomException;
use Illuminate\Support\Facades\Redis;
class VerificationService
{

    private const CODE_LIFETIME_MINUTES = 1;
    private const RESEND_TIMEOUT_MINUTES = 1;

    /**
     * @throws RandomException
     */
    public function sendVerificationCode(User $user): bool
    {
        try {
            app(SendUserCodeRabbitPublisher::class)->sendVerification($user);
            return true;
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

    public function verifyCode(User $user, string $user_code): bool
    {
        $send_code = Redis::connection('verification')->get('verification_code:' . $user->id);
        if ($send_code === $user_code) {
            Redis::connection('verification')->del('verification_code:' . $user->id);
            $user->markEmailAsVerified();
            return true;
        }
        return false;
    }
}
