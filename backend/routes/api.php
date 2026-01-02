<?php

use App\Http\Controllers\User\ResetPasswordController;
use App\Http\Controllers\User\SendVerificationCodeController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\VerifyEmailController;
use App\Http\Resources\User\UserResource;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Route;

//  без защиты
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
    //Смена пароля в лк то есть аторизированного пользователя
//    Route::post('/reset-password-auth/', [ResetPasswordController::class, 'passwordResetAuthUser'])
//        ->name('password.reset.auth')
//        ->middleware(['auth:sanctum']);

    Route::post('/me', function () {

        return new UserResource(auth()->user());
    })->middleware(['auth:sanctum']);


//    /*
//     * Отдаёт текущего пользователя с его постами пагинация на 10 постов
//     */
});
// 123
//Route::prefix('posts')->name('posts.')->group(function () {
//    // все посты на главную
//    Route::get('/', [PostController::class, 'index'])->name('index');
//    // посмотреть пост
//    Route::get('/{post}', [PostController::class, 'show'])->name('show');
//    // действия с постами авторизованного пользователя
//    Route::middleware('auth:sanctum')->group(function () {
//        Route::post('/', [PostController::class, 'store'])->name('store');
//        Route::put('/{post}', [PostController::class, 'update'])->name('update');
//        Route::delete('/{post}', [PostController::class, 'destroy'])->name('destroy');
//    });
//    // имаге в ресурс для отдачи в другой ресурс
//    Route::get('/images/{image}/view', [ImageController::class, 'view'])->name('images.view');
//    Route::get('/images/{image}/download', [ImageController::class, 'download'])->name('images.download');
//});
//Route::get('/check', function () {
//    return response()->json([
//        'authenticated' => Auth::check(),
//        'user' => Auth::user()
//    ]);
//})->middleware('auth:sanctum');
////  auth:sanctum и verified -> и если толька почта подтверждена
//Route::middleware(['auth:sanctum', 'verified'])->group(function () {
//    Route::get('/profile', function () {
//        // resolve удаляет data {}
//        return (new UserResource(auth()->user()))->resolve();
//    });
//
//    Route::get('/dashboard', function () {
//        return response()->json(['message' => 'Dashboard data']);
//    });
//});


// тест
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
