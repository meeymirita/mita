<?php

namespace App\Http\Controllers\User;

use App\Contracts\User\PasswordResetUserInterface;
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

    public PasswordResetUserInterface $passwordReset;

    public function __construct(
        PasswordResetUserInterface $passwordReset
    )
    {
        $this->passwordReset = $passwordReset;
    }

    /**
     * {
     * "email": "mirita1@gmail.com"
     * }
     * @param Request $request
     * @return JsonResponse
     */
    public function sendResetLink(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|exists:users,email',
            ], [
                'email.required' => 'Поля обязательно для заполнения',
                'email.exists' => 'Такой емаил не найден (',
            ]);

            $this->passwordReset->sendResetLink($validated['email']);

            return response()->json([
                'message' => 'Ссылка для сброса пароля отправлена на email',
            ], 200);
        } catch (\Exception $exception) {
            \Log::error('Произошла оишбка', [
                'email' => $request->email,
                'error' => $exception->getMessage()
            ]);

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
            $validatedData = $validator->validated();
            $this->passwordReset->passwordReset($validatedData);

            return response()->json([
                'message' => 'Пароль успешно изменен'
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Password reset error', [
                'token' => $request->token,
                'error' => $e->getMessage()
            ]);

            $statusCode = str_contains($e->getMessage(), 'Неверный') ||
            str_contains($e->getMessage(), 'Срок действия') ? 400 : 500;

            return response()->json([
                'message' => $e->getMessage()
            ], $statusCode);
        }
    }

}

