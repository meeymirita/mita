<?php

namespace App\Services\User;


use App\Contracts\UserCreateInterface;
use App\Enums\UserCheckLoginField;
use App\Enums\UserStatus;
use App\Enums\UserType;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * @param array $credentials
     * @return array
     */
    public function login(array $credentials): array
    {
        // валидация, реквест удалил сюда всё
        $this->validateCredentials($credentials);
        // проверка это логин или почта
        $fieldType = $this->checkFieldType($credentials['login']);
        // поиск юзера
        $user = $this->findUser($fieldType, $credentials['login']);
        // роверка пароля
        $this->validatePassword($credentials['password'], $user);
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
