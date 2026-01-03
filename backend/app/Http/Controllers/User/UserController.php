<?php

namespace App\Http\Controllers\User;

use App\Contracts\User\AuthUserInterface;
use App\Contracts\User\UpdateUserInterface;
use App\Contracts\User\UserCreateInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\User\RegisterAndLoginUserResponseResource;
use App\Http\Resources\User\RegisterResponseResource;
use App\Http\Resources\User\UserResource;
use App\Services\User\UpdateService;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public UserCreateInterface $userCreate;
    public AuthUserInterface $authService;
    public UpdateService $updateService;

    /**
     * @param UserCreateInterface $userCreate
     * @param AuthUserInterface $authService
     * @param UpdateUserInterface $updateService
     */
    public function __construct(
        UserCreateInterface $userCreate,
        AuthUserInterface   $authService,
        UpdateUserInterface $updateService
    )
    {
        $this->userCreate = $userCreate;
        $this->authService = $authService;
        $this->updateService = $updateService;
    }
    /**
     * @param CreateUserRequest $request
     * @return RegisterAndLoginUserResponseResource|JsonResponse
     */
    public function register(CreateUserRequest $request)
    {
        try {
            $data = $this->userCreate->createUser($request->validated());
            return new RegisterAndLoginUserResponseResource($data);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Ошибка регистрации', 'error' => $e->getMessage()], 500);
        }
    }
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $data = $this->authService->login($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Авторизация успешна',
                'data' => new RegisterAndLoginUserResponseResource($data)
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
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        // удаление всех токенов пользователя
        $request->user()->tokens()->delete();
        return response()->json([
            'status' => 'Вы вышли из аккаунта'
        ]);
    }
    /**
     * @param UpdateUserRequest $request
     * @return UserResource
     */
    public function update(UpdateUserRequest $request): UserResource
    {
        return UserResource::make($this->updateService->update($request->validated()));
    }
}
