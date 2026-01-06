<?php

namespace App\Models;

use Database\Factories\ArticleLikeFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[UseFactory(ArticleLikeFactory::class)]
class ArticleLike extends Model
{
    use HasFactory;
    protected $table = 'articles_users_likes';
    protected $fillable = [
        'article_id',
        'user_id',
    ];

    public function article(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Article::class, 'id');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
