<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Сброс пароля</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 40px 20px;
            background-color: #f5f5f5;
        }

        .email-wrapper {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            border-radius: 4px;
            padding: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .brand {
            font-size: 24px;
            font-weight: 600;
            margin: 0 0 10px 0;
        }

        .subject {
            font-size: 20px;
            font-weight: 500;
            margin: 0;
            color: #2d3748;
        }

        .greeting {
            font-size: 16px;
            margin-bottom: 15px;
            color: #4a5568;
        }

        .instruction {
            font-size: 15px;
            color: #718096;
            margin-bottom: 30px;
            line-height: 1.7;
        }

        .code-container {
            padding: 30px;
            text-align: center;
            margin: 30px 0;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            background-image: url('{{ $sakura_url }}');
        }

        .reset-link {
            font-size: 20px;
            font-weight: 600;
            color: #56256e;
            letter-spacing: 1px;
            font-family: 'SF Mono', Monaco, monospace;
            margin: 0;
            word-break: break-all;
            text-shadow: 2px 2px 4px rgba(197, 10, 10, 0.3);
        }

        .reset-link a {
            color:rgb(255, 255, 255);
            text-decoration: none;
            border-bottom: 2px dashed #ff6f6f;
            padding-bottom: 3px;
        }

        .reset-link a:hover {
            border-bottom: 2px solid #ff6f6f;
        }

        .expiry {
            font-size: 14px;
            color: #ffffff;
            margin-top: 15px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }

        .himary {
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 200px;
            margin: 30px 0;
            padding: 15px;
            border-radius: 4px;
        }

        .footer {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 14px;
            text-align: center;
        }

        .signature {
            font-size: 20px;
            font-weight: 500;
            margin: 10px 0;
            color: #495057;
        }

        .auto-generated {
            font-size: 12px;
            color: #adb5bd;
            margin-top: 20px;
        }

        .note-box {
            background-color: #f8f0ff;
            border-left: 4px solid #ff6f6f;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
<div class="email-wrapper">
    <div class="header">
        <div style="color: #ff6f6f; letter-spacing: 5px; margin: 0;" class="brand">meeymirita</div>
        <h2 class="subject">Сброс пароля</h2>
    </div>

    <p class="greeting">✨ Здравствуйте, {{ $user->name }}!</p>

    <p class="instruction">
        Кто-то (надеемся, что вы) запросил сброс пароля для вашей учётной записи. 
        Не волнуйтесь, даже у героев аниме иногда случаются провалы в памяти! 
    </p>

    <div class="code-container" style="background-image: url('{{ $sakura_url }}');">
        <div class="reset-link">
            🔑 <a href="{{ url('/reset-password/token?' . $token) }}">Восстановить доступ</a>
        </div>
        <p class="expiry" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">
            Ссылка действительна 30 минут
        </p>
    </div>

    <div class="note-box">
        <p class="instruction" style="margin-bottom: 0; color: #4a5568;">
            💫 Если вы не запрашивали сброс пароля — просто проигнорируйте это письмо. 
            Ваш аккаунт в безопасности, как убежище Саске в Наруто!
        </p>
    </div>

    <h2 class="subject">✨ Новое начало ✨</h2>
    <div>
        <p class="instruction">
            "Даже после самой тёмной ночи наступает рассвет" — так и с забытыми паролями. 
            Это всего лишь небольшое приключение на пути к новой главе!
        </p>
        <p class="instruction">
            Кстати, после восстановления пароля не забудьте сохранить его в надёжном месте. 
            А ещё лучше — запишите на свитке, как настоящий ниндзя! 📜
        </p>
    </div>

    <div class="himary" style="background-image: url('{{ $himary_url }}');"></div>

    <p class="instruction" style="text-align: center; font-size: 16px;">
        👉👈 Жми на ссылку и возвращайся к нам! 
        <br>
        <span style="color: #ff6f6f;">Ваше следующее приключение уже ждёт!</span>
    </p>

    <div class="footer">
        <p class="signature">С тёплыми пожеланиями,</p>
        <p>
            <strong>
                <a style="font-size: 22px; color: #ff6f6f; letter-spacing: 5px; margin: 0; text-decoration: none;"
                   href="{{ $frontend_url }}">
                    meeymirita
                </a>
            </strong>
        </p>
        <div class="auto-generated">
            Это письмо сгенерировано автоматически и не требует ответа.
            <br>
            🌸 Да пребудет с вами сила аниме! 🌸
        </div>
    </div>
</div>
</body>
</html>