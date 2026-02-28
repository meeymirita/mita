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
            'user_id' => 'required|integer|exists:users,id',
            'user_code' => 'required|string|size:6'
        ]);
        $user = User::query()->where('id', $request->get('user_id'))->first();
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
