<?php

use App\Http\Controllers\Car\CarController;
use App\Http\Controllers\User\ResetPasswordController;
use App\Http\Controllers\User\SendVerificationCodeController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\VerifyEmailController;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->name('user.')->group(callback: function () {
    //ругистрация с отправкой письма на почту для подтверждения её
    Route::post('/register', action: [UserController::class, 'register'])
        ->name('register');
    // Подтверждение email
    Route::post('/verify-email', [VerifyEmailController::class, 'verify'])
        ->name('verify-email');
    // Отправка кода
    Route::post('/send-verification-code', [SendVerificationCodeController::class, 'sendCode'])
        ->name('send-verification-code');
    // Повторная отправка кода
    Route::post('/resend-verification-code', [SendVerificationCodeController::class, 'resendCode'])
        ->name('resend-verification-code');
    // вход в аккаунт
    Route::post('/login', action: [UserController::class, 'login'])
        ->name('login');
    // выход из аккаунта
    Route::post('/logout', action: [UserController::class, 'logout'])
        ->middleware(['auth:sanctum'])
        ->name('logout');
    // обновление данных пользовтеля
    Route::post('update', [UserController::class, 'update'])
        ->middleware(['auth:sanctum'])
        ->name('update');
    // ссылка на сброс пароля
    Route::post('/send-reset-link', [ResetPasswordController::class, 'sendResetLink'])
        ->name('password.email');
    //Смена пароля по http://localhost:5173/reset-password/token?GDd7UzzC2uohvSkCiQBHH7jVfaXCDiWQJOwXXVgVQp6JhEYYkcKcg9q0x7Ki
    Route::post('/reset-password/', [ResetPasswordController::class, 'passwordReset'])
        ->name('password.reset');

    Route::get('/me', function () {
        return new UserResource(auth()->user());
    })->middleware(['auth:sanctum']);

});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/available-cars', [CarController::class, 'getAvailableCars']);
});
