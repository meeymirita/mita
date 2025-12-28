<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserStatus;
use App\Enums\UserType;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Random\RandomException;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
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
        'verification_code_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'type' => UserType::class,
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * связь с постами 1 юзер много постов
     * @return HasMany
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

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
            'verification_code_expires_at' => now()->addDays(30),
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


}
