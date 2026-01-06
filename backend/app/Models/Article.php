<?php

namespace App\Models;

use Database\Factories\ArticleFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// https://laravel.com/docs/12.x/eloquent-factories
#[UseFactory(ArticleFactory::class)]
class Article extends Model
{
    use HasFactory;
    protected $table = 'articles';
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    /**
    * Relationships
    */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likes(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'articles_users_likes')
            ->withTimestamps();
    }

}
