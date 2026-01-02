<?php

namespace App\Services\User;


use App\Contracts\User\PasswordResetUserInterface;
use App\Events\SendResetLinkEvent;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PasswordResetService implements PasswordResetUserInterface
{
    private const TOKEN_LIFETIME_MINUTES = 60;

    /**
     * @return array{success: true, token: string}
     */
    public function sendResetLink(string $userData) : array
    {
        // поиск пользователя
        $user = $this->findUserForEmail($userData);
        if (!$user) {
            throw new \Exception('Пользователь не найден');
        }
        // удаление токенов у него
        $this->deleteTokenFromEmail($user->email);
        // генерация нового
        $token = Str::random(60);
        // по емаил находим и вставялем токен
        $this->setTokenUser($user, $token);

        try {
            // отправка письма с токеном
            SendResetLinkEvent::dispatch($user, $token);

            return ['success' => true, 'token' => $token];

        } catch (\Exception $e) {
            throw new \Exception('Ошибка при отправке email: ' . $e->getMessage());
        }
    }

    /**
     * @return array{success: true}
     */
    public function passwordReset(array $userData) : array
    {
        $token = $userData['token'];
        $password = $userData['password'];
        $resetRecord = $this->getValidResetToken($token);

        if (!$resetRecord) {
            throw new \Exception('Неверный или просроченный токен');
        }

        $tokenCreatedAt = Carbon::parse($resetRecord->created_at);

        if ($tokenCreatedAt->diffInMinutes(now()) > self::TOKEN_LIFETIME_MINUTES) {
            $this->deleteTokenFromEmail($resetRecord->email);
            throw new \Exception('Срок действия токена истек');
        }

        $user = $this->findUserForEmail($resetRecord->email);

        if (!$user) {
            throw new \Exception('Пользователь не найден');
        }

        $user->password = Hash::make($password);

        $user->save();

        $this->deleteTokenFromEmail($resetRecord->email);

        return ['success' => true];
    }


    /**
     * Валидация токена
     * @param string $token
     * @return object|null
     */
    public function getValidResetToken(string $token): ?object
    {
        if (!preg_match('/^[a-zA-Z0-9]+$/', $token) || strlen($token) > 60) {
            return null;
        }

        $record = DB::table('password_reset_tokens')
            ->where('token', hash('sha256', $token))
            ->first();

        return $record;
    }
    /**
     * поиск пользователя
     * @param string $email
     * @return User|null
     */
    public function findUserForEmail(string $email): ?User
    {
        return User::query()->where('email', $email)->first();
    }

    /**
     * добавление токена
     * @param User $user
     * @param string $token
     * @return bool
     */
    public function setTokenUser(User $user, string $token): bool
    {
        return DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => hash('sha256', $token),
                'created_at' => now(),
            ]
        );
    }
    /**
     * удаление токена по емаил
     * @param string $email
     * @return bool
     */
    public function deleteTokenFromEmail(string $email): bool
    {
        return DB::table('password_reset_tokens')
            ->where('email', $email)
            ->delete();
    }
}
