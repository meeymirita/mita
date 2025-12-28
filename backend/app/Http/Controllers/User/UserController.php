<?php

namespace App\Http\Controllers\User;

use App\Contracts\UserCreateInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Resources\User\LoginResponseResource;
use App\Http\Resources\User\RegisterResponseResource;
use App\Services\User\AuthService;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class UserController extends Controller
{
    public UserCreateInterface $userCreate;
    public AuthService $authService;

    public function __construct(
        UserCreateInterface $userCreate,
        AuthService         $authService
    )
    {
        $this->userCreate = $userCreate;
        $this->authService = $authService;
    }

    public function register(CreateUserRequest $request)
    {
        try {
            $data = $this->userCreate->createUser($request->validated());
            return new RegisterResponseResource($data);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Ошибка регистрации', 'error' => $e->getMessage()], 500);
        }
    }

    public function login(Request $request): JsonResponse
    {
        try {
            $data = $this->authService->login($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Авторизация успешна',
                'data' => new LoginResponseResource($data)
            ], 200);


        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибки валидации',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка сервера',
                'error' => $e->getMessage() ?: null
            ], 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        // удаление всех токенов пользователя
        $request->user()->tokens()->delete();
        return response()->json([
            'status' => 'Вы вышли из аккаунта'
        ]);
    }

}
//
//public function login(Request $request): JsonResponse
//{
//    try {
//        $data = $this->userLoginService->login($request->all());
//
//        return response()->json([
//            'success' => true,
//            'message' => 'Авторизация успешна',
//            'data' => new LoginResponseResource($data)
//        ], 200);
//
//
//    } catch (ValidationException $e) {
//        return response()->json([
//            'success' => false,
//            'message' => 'Ошибки валидации',
//            'errors' => $e->errors()
//        ], 422);
//    } catch (\Exception $e) {
//        return response()->json([
//            'success' => false,
//            'message' => 'Ошибка сервера',
//            'error' => $e->getMessage() ?: null
//        ], 500);
//    }
//}
//
//public function update(UpdateUserRequest $request): JsonResponse
//{
//    $user = auth()->user();
//    // UserServiceProvider
//    if (!$user->can('update', $user)) {
//        return response()->json([
//            'message' => 'У вас нет прав для обновления профиля'
//        ], 403);
//    }
//    $data = $request->validated();
//    $user->update($data);
//    return response()->json([
//        'message' => 'Данные успешно обновлены',
//        'user' => new UserResource($user->fresh()) // свежие данные из БД
//    ], 200);
//}
//
//public function sendResetLink(Request $request)
//{
//    $request->validate(['email' => 'required|email']);
//    $status = Password::sendResetLink(
//        $request->only('email')
//    );
//    return $status === Password::ResetLinkSent
//        ? response()->json(['status' => __($status)], 200)
//        : response()->json(['email' => __($status)], 422);
//}
//
//public function passwordReset(Request $request, $token)
//{
//    $request->validate([
//        'email' => 'required|email',
//        'password' => 'required|min:8|confirmed',
//    ]);
//
//    $status = Password::reset(
//        array_merge($request->only('email', 'password', 'password_confirmation'), ['token' => $token]),
//        function (User $user, string $password) {
//            $user->forceFill([
//                'password' => Hash::make($password)
//            ]);
//
//            $user->save();
//            $user->tokens()->delete();
//            event(new PasswordReset($user));
//        }
//    );
//
//    return $status === Password::PasswordReset
//        ? response()->json(['status' => __($status)], 200)
//        : response()->json(['status' => __($status)], 422);
//}
//
//public function logout(Request $request): JsonResponse
//{
//    // удаление всех токенов пользователя
//    $request->user()->tokens()->delete();
//    return response()->json([
//        'status' => 'Вы вышли из аккаунта'
//    ]);
//}
