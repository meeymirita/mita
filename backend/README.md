для картинок
"spatie/laravel-medialibrary": "^11.17",
// Получить URL аватара
$user->getFirstMediaUrl('avatars'); // оригинал
$user->getFirstMediaUrl('avatars', 'thumb'); // миниатюра

// Получить все размеры (responsive images)
$user->getFirstMedia('avatars')->getResponsiveImageUrls();

// Проверить наличие аватара
$user->hasMedia('avatars');

// Удалить аватар
$user->clearMediaCollection('avatars')

Для импорта экспорта в эксель
"maatwebsite/excel:^3.1"
