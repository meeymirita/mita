<?php

namespace App\Contracts\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
interface ArticleIndexPageInterface
{
    public function index(int $page = 1, int $perPage = 5) : LengthAwarePaginator;
}
