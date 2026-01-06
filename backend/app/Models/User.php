<?php

namespace App\Models;

use App\Enums\UserStatus;
use App\Enums\UserType;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Random\RandomException;
use Spatie\Image\Enums\CropPosition;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;


#[UseFactory(UserFactory::class)]
class User extends Authenticatable implements HasMedia
{
    use HasFactory, Notifiable, HasApiTokens, InteractsWithMedia;
    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'login',
        'type',
        'email',
        'password',
        'status',
        'email_verified_at',
        'verification_code',
        'verification_code_expires_at'
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @var array
     */
    protected $attributes = [
        'type' => UserType::User->value,
        'status' => UserStatus::Pending->value,
    ];
    /**
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    /**
    * Relationships
    */
    public function articles(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Article::class);
    }

    /**
    * Методы
    */

    /**
     * Генерация кода подтверждения
     * @return string
     * @throws RandomException
     */
    public function generateVerificationCode(): string
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->update([
            'verification_code' => Hash::make($code),
            'verification_code_expires_at' => now()->addMinutes(5), // 5 минут
        ]);
        return $code;
    }
    /**
     * Проверка кода подтверждения
     * @param string $code
     * @return bool
     */
    public function verifyCode(string $code): bool
    {
        if (!$this->verification_code || $this->verification_code_expires_at < now()) {
            return false;
        }

        return Hash::check($code, $this->verification_code);
    }
    /**
     * Подтверждение email
     * @return void
     */
    public function markEmailAsVerified():void
    {
        $this->update([
            'email_verified_at' => now(),
            'status' => UserStatus::Active->value,
            'verification_code' => null,
            'verification_code_expires_at' => null,
        ]);
    }
    /**
     * Проверка подтвержден ли email
     * @return bool
     */
    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }
    /**
    * https://timeweb.cloud/tutorials/cloud/ispolzovanie-laravel-s-s3-i-spatie-medialibrary
    * Библиотека для картинок
    */
    public function registerMediaCollections() : void
    {
        // Создаёт коллекцию с именем avatars, которая предназначена для хранения одного изображения
        // singleFile(): Гарантирует, что в коллекции будет храниться только один файл. Если добавить новый файл,
        // предыдущий будет автоматически удалён.
        // withResponsiveImages(): Автоматически генерирует адаптивные изображения (разные размеры для разных экранов)
        $this->addMediaCollection('avatars')
            ->singleFile();
            // много разных картинок будет
//            ->withResponsiveImages();

    }
    // addMediaConversion('thumb') Создаёт конверсию с именем thumb (миниатюра).
    // width(150) и height(150): Устанавливают размеры преобразованного изображения — 150x150 пикселей.
    // sharpen(10): Применяет резкость с уровнем 10, чтобы сделать изображение чётче.
    // performOnCollections('avatars'): Указывает, что миниатюра должна создаваться для всех изображений,
    // добавленных в коллекции avatars.
    public function registerMediaConversions(Media $media = null): void
    {
        // профиль
        $this->addMediaConversion('profile')
            ->crop(200, 200, CropPosition::Center)
            ->quality(85) // качество 85%
            ->sharpen(10) // легкая резкость
            ->performOnCollections('avatars');

        // в ленту
        $this->addMediaConversion('post')
            ->crop(100, 100, CropPosition::Top)
            ->quality(80)
            ->sharpen(8)
            ->performOnCollections('avatars');

        // в комментари
        $this->addMediaConversion('comment')
            ->crop(40, 40, CropPosition::Center)
            ->quality(75)
            ->sharpen(5)
            ->performOnCollections('avatars');

        // в уведомления
        $this->addMediaConversion('xs')
            ->crop(24, 24, CropPosition::Top)
            ->quality(70)
            ->sharpen(3)
            ->performOnCollections('avatars');
    }
}
