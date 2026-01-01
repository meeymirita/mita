<?php

namespace App\Http\Controllers\User;

use App\Events\SendResetLinkEvent;
use App\Http\Controllers\Controller;
use App\Mail\SendMailreset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;


class ResetPasswordController extends Controller{


    public function sendResetLink(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Поля обязательно для заполнения',
            'email.exists' => 'Такой емаил не найден (',
        ]);
        $user = User::query()->where('email', $request->email)->first();

        $token = Str::random(60);

        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => hash('sha256', $token),
                'created_at' => now(),
            ]
        );

        SendResetLinkEvent::dispatch($user, $token);
        // 1. Валидация email +
        // 2. Поиск пользователя +
        // 3. Генерация токена +
        // 4. Отправка письма (без route('password.reset'))
        // 5. Ответ JSON
    }

    public function passwordReset(string $token)
    {

    }
}

