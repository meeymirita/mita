<?php

namespace App\Services\User;


use App\Contracts\User\AuthUserInterface;
use App\Enums\UserCheckLoginField;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthService implements AuthUserInterface
{
    /**
     * @param array $userData
     * @return array
     */
    public function login(array $userData): array
    {
        // валидация,
        $this->validateCredentials($userData);
        // проверка это логин или почта
        $fieldType = $this->checkFieldType($userData['login']);
        // поиск юзера
        $user = $this->findUser($fieldType, $userData['login']);
        // роверка пароля
        $this->validatePassword($userData['password'], $user);
        // авториазция
        Auth::login($user);

        return $this->createAuthResponse($user);
    }

    /**
     * @param array $credentials
     * @return void
     */
    private function validateCredentials(array $credentials): void
    {
        $validator = Validator::make($credentials, [
            'login' => 'required|string|min:3',
            'password' => 'required|string|min:3',
        ], [
            'login.required' => 'Поле логина обязательно для заполнения',
            'login.min' => 'Логин должен содержать минимум :min символа',
            'password.required' => 'Поле пароля обязательно для заполнения',
            'password.min' => 'Пароль должен содержать минимум :min символа',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }


    /**
     * @param string $login
     * @return string
     */
    private function checkFieldType(string $login)
    {
        return filter_var($login, FILTER_VALIDATE_EMAIL)
            ? UserCheckLoginField::Email->value
            : UserCheckLoginField::Login->value;
    }

    /**
     * @param string $fieldType
     * @param string $fieldValue
     * @return User
     */
    private function findUser(string $fieldType, string $fieldValue): User
    {
        $user = User::query()->where($fieldType, $fieldValue)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'login' => ['Пользователь с таким логином/email не найден'],
            ]);
        }

        return $user;
    }

    /**
     * @param string $password
     * @param User $user
     * @return void
     */
    private function validatePassword(string $password, User $user): void
    {
        if (!Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['Неверный пароль'],
            ]);
        }
    }

    /**
     * @param User $user
     * @return array
     */
    private function createAuthResponse(User $user): array
    {
        $token = $user->createToken(
            'auth_token',
            ['user'],
            now()->addWeek()
        )->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => now()->addWeek()->toISOString(),
        ];
    }


}
