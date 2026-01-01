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

        \DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->delete();

        $token = Str::random(60);

        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => hash('sha256', $token),
                'created_at' => now(),
            ]
        );

        try {
            SendResetLinkEvent::dispatch($user, $token);

            return response()->json([
                'message' => 'Ссылка для сброса пароля отправлена на email'
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Ошибка отправки ссылки сброса пароля', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Произошла ошибка при отправке email'
            ], 500);
        }
    }

    public function passwordReset(string $token)
    {
        $resetRecord = \DB::table('password_reset_tokens')
            ->where('token', hash('sha256', $token))
            ->first();
        dd($resetRecord);
    }
}

