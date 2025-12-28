<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\User\VerificationService;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    public function __construct(
        private VerificationService $verificationService
    )
    {
    }

    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string|size:6'
        ]);

        $user = User::query()->where('email', $request->get('email'))->first();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'Email уже подтвержден'
            ], 400);
        }

        $verification = $this->verificationService->verifyCode($user, $request->code);
        if (!$verification) {
            return response()->json([
                'success' => false,
                'message' => 'Неверный или просроченный код'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Email успешно подтвержден',
            'token' => $user->token
        ]);
    }
}
