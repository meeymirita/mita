<?php

namespace App\Http\Controllers\User;

use App\Events\SendResetLinkEvent;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class ResetPasswordController extends Controller
{
    /**
     * {
     * "email": "mirita1@gmail.com"
     * }
     * @param Request $request
     * @return JsonResponse
     */
    public function sendResetLink(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Поля обязательно для заполнения',
            'email.exists' => 'Такой емаил не найден (',
        ]);
        // поиск пользователя
        $user = $this->findUserForEmail($request->email);
        // удаление токенов у него
        $this->deleteTokenFromEmail($user->email);
        // генерация нового
        $token = Str::random(60);
        // по емаил находим и вставялем токен
        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => hash('sha256', $token),
                'created_at' => now(),
            ]
        );

        try {
            // отправка письма с токеном
            SendResetLinkEvent::dispatch($user, $token);

            return response()->json([
                'message' => 'Ссылка для сброса пароля отправлена на email'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Произошла ошибка при отправке email'
            ], 500);
        }
    }

    /**
     * принимает
     * {
     * "token": "ObEBmnClSHTHQeTbQ8ESY9vZ27371FTQjOIEGTpqz3jUOvUJfQTbdOqaKgna",
     * "password": "qweqweqweqw",
     * "password_confirmation": "qweqweqweqw"
     * }
     * @param Request $request
     * @return JsonResponse
     */
    public function passwordReset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string|max:60',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
        ], [
            'password.required' => 'Пароль обязателен',
            'password.min' => 'Пароль должен быть минимум 8 символов',
            'password.confirmed' => 'Пароли не совпадают',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        try {
            $resetRecord = $this->getValidResetToken($request->token);
            if (!$resetRecord) {
                return response()->json([
                    'error' => 'Неверный или просроченный токен'
                ], 400);
            }

            $tokenCreatedAt = Carbon::parse($resetRecord->created_at);
            if ($tokenCreatedAt->diffInMinutes(now()) > 60) {
                $this->deleteTokenFromEmail($resetRecord->email);
                return response()->json([
                    'error' => 'Срок действия токена истек'
                ], 400);
            }
            $user = $this->findUserForEmail($resetRecord->email);
            if (!$user) {
                return response()->json([
                    'error' => 'Пользователь не найден'
                ], 404);
            }
            $user->password = Hash::make($request->password);

            $user->save();

            $this->deleteTokenFromEmail($resetRecord->email);

            return response()->json([
                'message' => 'Пароль успешно изменен'
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Ошибка при сбросе пароля', [
                'token' => $request->token,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Произошла ошибка при сбросе пароля'
            ], 500);
        }
    }

    /**
     * Валидация токена
     * @param string $token
     * @return string|null
     */
    public function getValidResetToken(string $token)
    {
        if (!preg_match('/^[a-zA-Z0-9]+$/', $token) || strlen($token) > 60) {
            return null;
        }
        // может быть нулл
        $record = \DB::table('password_reset_tokens')
            ->where('token', hash('sha256', $token))
            ->first();

        return $record;
    }

    // поиск пользователя по емаил
    public function findUserForEmail(string $email)
    {
        return User::query()->where('email', $email)->first();
    }

    // удаление токена по емаил
    public function deleteTokenFromEmail(string $email)
    {
        return \DB::table('password_reset_tokens')
            ->where('email', $email)
            ->delete();
    }
}

