<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Код подтверждения</title>
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
            /* УДАЛИ background-image ОТСЮДА */
            padding: 30px;
            text-align: center;
            margin: 30px 0;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .verification-code {
            font-size: 36px;
            font-weight: 700;
            color: #212529;
            letter-spacing: 6px;
            font-family: 'SF Mono', Monaco, monospace;
            margin: 0;
        }

        .expiry {
            font-size: 14px;
            color: #ffffff;
            margin-top: 10px;
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
    </style>
</head>
<body>
<div class="email-wrapper">
    <div class="header">
        <div style="color: #ff6f6f; letter-spacing: 5px; margin: 0;" class="brand">meeymirita</div>
        <h2 class="subject">Код подтверждения</h2>
    </div>

    <p class="greeting">Уважаемый пользователь!</p>

    <p class="instruction">
        Чтобы продолжить регистрацию учётной записи, используйте следующий код подтверждения.
    </p>

    <div class="code-container" style="background-image: url('{{ $sakura_url }}');">
        <h1 style="color: #ffffff; letter-spacing: 5px; margin: 0; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);" class="verification-code">{{ $code }}</h1>
        <p class="expiry" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">Код действителен 30 минут</p>
    </div>

    <h2 class="subject">Начало прекрасной истории 📖</h2>
    <div>
        <p class="instruction">Помнишь момент, когда судьбы двух людей пересекаются?</p>
        <p class="instruction">Этот момент — сейчас. Добро пожаловать туда,
            где начинаются настоящие связи.</p>
    </div>
    <p class="instruction">Теперь это мы с тобой! 👉👈</p>

    <div class="himary" style="background-image: url('{{ $himary_url }}');"></div>

    <div class="footer">
        <p class="signature">С уважением,</p>
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
        </div>
    </div>
</div>
</body>
</html>
