<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\User\VerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SendVerificationCodeController extends Controller
{
    public function __construct
    (private VerificationService $verificationService)
    {
    }

    /**
     * Отправка кода подтверждения
     * @param Request $request
     * @return JsonResponse
     */
    public function sendCode(Request $request) : JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::query()->where('email', $request->get('email'))->first();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'Email уже подтвержден'
            ], 400);
        }

        try {
            $this->verificationService->sendVerificationCode($user);
            return response()->json([
                'success' => true,
                'message' => 'Код подтверждения отправлен на email'
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Повторная отправка кода
     * @param Request $request
     * @return JsonResponse
     */
    public function resendCode(Request $request) : JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::query()->where('email', $request->get('email'))->first();

        try {
            $this->verificationService->resendVerificationCode($user);

            return response()->json([
                'success' => true,
                'message' => 'Код подтверждения отправлен повторно'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
