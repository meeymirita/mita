<?php

use App\Http\Controllers\Article\ArticleController;
use App\Http\Controllers\User\ResetPasswordController;
use App\Http\Controllers\User\SendVerificationCodeController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\VerifyEmailController;
use App\Http\Resources\User\UserResource;
use App\Models\Article;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

//  без защиты
Route::prefix('user')->name('user.')->group(callback: function () {
    //ругистрация с отправкой письма на почту для подтверждения её
    Route::post(uri: '/register', action: [UserController::class, 'register'])
        ->name(name: 'register');
    // Подтверждение email
    Route::post(uri: '/verify-email', action: [VerifyEmailController::class, 'verify'])
        ->name(name: 'verify-email');
    // Отправка кода
    Route::post(uri: '/send-verification-code', action: [SendVerificationCodeController::class, 'sendCode'])
        ->name(name: 'send-verification-code');
    // Повторная отправка кода
    Route::post(uri: '/resend-verification-code', action: [SendVerificationCodeController::class, 'resendCode'])
        ->name(name: 'resend-verification-code');
    // вход в аккаунт
    Route::post(uri: '/login', action: [UserController::class, 'login'])
        ->name(name: 'login');
    // выход из аккаунта
    Route::post(uri: '/logout', action: [UserController::class, 'logout'])
        ->middleware(['auth:sanctum'])
        ->name(name: 'logout');
    // обновление данных пользовтеля
    Route::post(uri: 'update', action: [UserController::class, 'update'])
        ->middleware(['auth:sanctum'])
        ->name(name: 'update');
    // ссылка на сброс пароля
    Route::post(uri: '/send-reset-link', action: [ResetPasswordController::class, 'sendResetLink'])
        ->name(name: 'password.email');
    //Смена пароля по http://localhost:5173/reset-password/token?GDd7UzzC2uohvSkCiQBHH7jVfaXCDiWQJOwXXVgVQp6JhEYYkcKcg9q0x7Ki
    Route::post(uri: '/reset-password/', action: [ResetPasswordController::class, 'passwordReset'])
        ->name(name: 'password.reset');
    //Смена пароля в лк то есть аторизированного пользователя
//    Route::post('/reset-password-auth/', [ResetPasswordController::class, 'passwordResetAuthUser'])
//        ->name('password.reset.auth')
//        ->middleware(['auth:sanctum']);

    Route::get('/me', function () {
        $user = Auth::user();
        return response()->json([
            'data' => new UserResource($user),
        ]);
    })->middleware(['auth:sanctum']);
//    Либу поставить для реалтайм уводомлений
//https://github.com/levskiy0/laravel-long-polling
});

Route::prefix('article')->name(value: 'article.')->group(callback: function () {
    Route::get(uri: '/', action: [ArticleController::class, 'index']);
});
Route::get('/article_like', function () {

    $post = Article::find(1);
    return response()->json($post->likes);

});
// тест
// Тестовый роут для проверки очереди
Route::get('/get_user/{id}', function ($id) {
    return Cache::get("users:user_{$id}");
});
Route::get('/test-queue-mass', function () {
    \Log::info('=== НАЧАЛО массовой отправки в очередь ===');

    $user = \App\Models\User::first();

    if (!$user) {
        return response('Пользователь не найден', 404);
    }

    $start = microtime(true);

    // Отправляем 100 событий
    for ($i = 1; $i <= 1000; $i++) {
        event(new \App\Events\VerificationCodeMailEvent($user, 'TEST-' . $i));

        // Логируем каждые 100 итераций
        if ($i % 100 === 0) {
            \Log::info("Отправлено событий: {$i}");
        }
    }

    $time = microtime(true) - $start;
    \Log::info('=== КОНЕЦ массовой отправки ===', [
        'total' => 1000,
        'time_seconds' => round($time, 2),
        'events_per_second' => round(1000 / $time, 2)
    ]);

    return response()->json([
        'message' => 'Отправлено 1000 событий в очередь',
        'time_seconds' => round($time, 2),
        'events_per_second' => round(1000 / $time, 2),
        'queue' => 'emails_queue',
        'user_id' => $user->id,
        'timestamp' => now()
    ]);
});
Route::get('/test', function () {
    return 'asd';
});
Route::get('/debug-email-urls', function () {
    $data = [
        'app_url' => config('app.url'),
        'current_url' => url('/'),
        'full_url' => url()->full(),
        'scheme_and_host' => request()->getSchemeAndHttpHost(),
        'root' => url(''),

        'asset_url_himary' => asset('storage/images/himary.jpg'),
        'url_himary' => url('storage/images/himary.jpg'),
    ];

    return response()->json($data);
});
Route::get('/test-email', function () {
    $user = \App\Models\User::first();

    if (!$user) {
        $user = \App\Models\User::create([
            'email' => 'tesйцуt@example.com',
            'password' => bcrypt('password'),
        ]);
    }

    return (new \App\Mail\VerificationCodeMail($user, '123156'))->render();
});
Route::get('/show-image', function () {
    $path = storage_path('app/public/images/himary.jpg');

    return response()->file($path, [
        'Content-Type' => 'image/jpeg',
        'Content-Disposition' => 'inline; filename="himary.jpg"'
    ]);
});
Route::get('/test-path', function () {
    try {
//        \Mail::raw('Тестовое письмо из Laravel', function ($message) {
//            $email = 'nik.lyamkin@yandex.ru';
//            //роверил в сообщения так ставить storage_path('app/public/me.jpg')
//            // Attachment::fromPath из доки https://laravel.com/docs/12.x/mail
//            $attachment = Attachment::fromPath(public_path('himary.jpg'));
//            // к меседжу
//            $message->attach($attachment);
//
//            $message->to($email)
//                ->subject('Тест отправки почты');
//        });

        return response()->json([
            'storage_path' => storage_path('sakura.png'),
            'app_path' => app_path(),         // Путь к app/
            'public_path' => public_path(storage_path('sakura.png')),   // Путь к public/
            'base_path' => base_path(),       // Корень проекта
        ]);
    } catch (\Exception $e) {
        return 'Ошибка: ' . $e->getMessage();
    }
});
Route::get('/test-queueqqq', function () {
    $user = App\Models\User::first();

    if (!$user) {
        return 'No users found';
    }
    $q = config('rabbitmq.queues.emails');
    for ($i = 0; $i < 111; $i++) {
        App\Events\VerificationCodeMailEvent::dispatch(
            $user, 123, $q
        );
    }

    return '.';
});
Route::get('/check-image-path', function () {
    $paths = [
        'storage_path' => storage_path('app/public/imagesToEmails/registerImage/himary.jpg'),
        'public_storage' => public_path('storage/imagesToEmails/registerImage/himary.jpg'),
        'exists_storage' => file_exists(storage_path('app/public/imagesToEmails/registerImage/himary.jpg')),
        'exists_public' => file_exists(public_path('storage/imagesToEmails/registerImage/himary.jpg')),
    ];

    return response()->json($paths);
});
Route::get('/check-queue-config', function () {
    return [
        'default_connection' => config('queue.default'),
        'rabbitmq_config' => config('queue.connections.rabbitmq'),
        'env_variables' => [
            'QUEUE_CONNECTION' => env('QUEUE_CONNECTION'),
            'RABBITMQ_QUEUE' => env('RABBITMQ_QUEUE'),
            'RABBITMQ_HOST' => env('RABBITMQ_HOST'),
        ],
        'available_queues' => config('rabbitmq.queues', ['high', 'default', 'low'])
    ];
});
Route::get('/test-mail', function () {
    try {
        \Mail::raw('Тестовое письмо из Laravel', function ($message) {
            $email = 'nik.lyamkin@yandex.ru';
            //роверил в сообщения так ставить storage_path('app/public/me.jpg')
            // Attachment::fromPath из доки https://laravel.com/docs/12.x/mail
            $attachment = Attachment::fromPath(storage_path('app/public/me.jpg'));
            // к меседжу
            $message->attach($attachment);

            $message->to($email)
                ->subject('Тест отправки почты');
        });

        return 'Письмо отправлено!';
    } catch (\Exception $e) {
        return 'Ошибка: ' . $e->getMessage();
    }
});
// docker-compose -f docker-compose.prod.yml exec laravel bash лара
// docker-compose -f docker-compose.prod.yml up -d поднять
//mysql
//  docker-compose -f docker-compose.prod.yml exec mysql bash
// mysql -u root -p / password
// mysql> use laravel
// mysql> DELETE FROM users WHERE email = 'nik.lyamkin@yandex.ru';
