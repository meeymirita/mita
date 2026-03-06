<?php

namespace App\Services\User;

use App\Models\User;
use App\Rabbit\User\SendUserCodeRabbitPublisher;
use Random\RandomException;
use Illuminate\Support\Facades\Redis;
class VerificationService
{
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
    }

    /**
     * @param User $user
     * @return bool
     * @throws RandomException
     */
    public function resendVerificationCode(User $user): bool
    {
        $ttl = Redis::connection('verification')->ttl('verification_code:' . $user->id);

        if ($ttl > 0) {
            $secondsLeft = $ttl;
            $minutesLeft = ceil($secondsLeft / 60);

            throw new \Exception('Повторный код можно запросить через ' . $minutesLeft . ' секунд(ы)');
        } else {
            return $this->sendVerificationCode($user);
        }
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
