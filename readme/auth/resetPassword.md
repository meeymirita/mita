====================
ССЫКА НА СБРОС ПАРОЛЯ ПОЛЬЗОВАТЕЛЯ УХОДИТ НА ПОЧТУ С ПИСЬМЕ
====================

Метод: POST
Эндпоинт: /api/user/reset-password/

http://localhost:5173/reset-password/token?GDd7UzzC2uohvSkCiQBHH7jVfaXCDiWQJOwXXVgVQp6JhEYYkcKcg9q0x7Ki

При загрузке страницы сброса пароля

const urlParams = new URLSearchParams(window.location.search); \
const token = urlParams.get('token');

Тело запроса (JSON):
{
"token": "ObEBmnClSHTHQeTbQ8ESY9vZ27371FTQjOIEGTpqz3jUOvUJfQTbdOqaKgna",
"password": "qweqweqweqw",
"password_confirmation": "qweqweqweqw"
}

1. Token:
    - Обязательное поле
    - Максимум 60 символов

2. Password:
    - Обязательное поле
    - Минимум 3 символа
    - Должен совпадать с password_confirmation


