<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Сброс пароля</title>
</head>
<body>
<div class="email-wrapper">
    <h2>Сброс пароля</h2>
    <p>Здравствуйте, {{ $user->name }}!</p>
    <p>Для сброса пароля перейдите по ссылке:</p>
    <a href="{{ url('/reset-password/' . $token) }}">
        Сбросить пароль
    </a>
    <p>Если вы не запрашивали сброс пароля, проигнорируйте это письмо.</p>
    <p>Ссылка действительна в течение 60 минут.</p>
</div>
</body>
</html>
