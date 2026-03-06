<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\User\VerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    public function __construct(
        private VerificationService $verificationService
    ){}
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'user_code' => 'required|string|size:6'
        ]);
        $user = User::query()->where('email', $request->get('email'))->first();
        $is_verified = $this->verificationService->verifyCode(user: $user, user_code: $request->get('user_code'));
        if ($is_verified) {
            return response()->json([
                'success' => true,
                'message' => 'Email успешно подтвержден',
            ]);
        } 
        return response()->json([
            'success' => false,
            'message' => 'Неверный или просроченный код'
        ], 400);
    }
}
