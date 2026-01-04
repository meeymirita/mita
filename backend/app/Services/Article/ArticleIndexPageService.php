<?php

namespace App\Services\Article;


use App\Contracts\Article\ArticleIndexPageInterface;
use App\Models\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ArticleIndexPageService implements ArticleIndexPageInterface
{
    public function index(int $page = 1, int $perPage = 5): LengthAwarePaginator
    {
        return Article::query()
            ->select(['id', 'user_id', 'title', 'slug', 'description', 'created_at'])
            ->with(['user' => fn($q) => $q->select('id', 'login', 'type')])
            ->latest(column: 'id')
            ->paginate($perPage, ['*'], 'page', $page);
    }
}
