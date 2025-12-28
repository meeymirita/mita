<?php

namespace App\Services\User;


use App\Contracts\UserCreateInterface;
use App\Enums\UserStatus;
use App\Enums\UserType;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserCreateService implements UserCreateInterface
{

    public function __construct(
        private VerificationService $verificationService
    ) {}

    /**
     * @param array $userData
     * @return array|mixed
     * @throws \Throwable
     */
    public function createUser(array $userData): mixed
    {
        return DB::transaction(function () use ($userData) {
            $user = User::query()->create([
                'email' => $userData['email'],
                'type' => UserType::User->value,
                'status' => UserStatus::Pending->value, // Не подтвержден
                'password' => Hash::make($userData['password']),
            ]);
            $token = $user->createToken(
                'user_register', ['*'], now()->plus(weeks: 1)
            )->plainTextToken;
            try {
                // Отправляем код подтверждения
                $this->verificationService->sendVerificationCode($user);
            } catch (\Exception $e) {
                \Log::error('e', [
                    'error' => $e->getMessage(),
                    'user_id' => $user->id
                ]);
            }

            return [
                'user' => $user,
                'token' => $token,
            ];
        });
    }

}
