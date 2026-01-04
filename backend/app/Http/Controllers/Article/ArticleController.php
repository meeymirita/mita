<?php

namespace App\Http\Controllers\Article;

use App\Contracts\Article\ArticleIndexPageInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Article\StoreArticleRequest;
use App\Http\Requests\Article\UpdateArticleRequest;
use App\Http\Resources\Article\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ArticleController extends Controller
{
    private const CACHE_TTL = 120;
    public function __construct(
        private readonly ArticleIndexPageInterface $articleIndexPage,
    ){}
    public function index(Request $request) : \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 5);
        $cacheKey = "articles:page:{$page}:perpage:{$perPage}";
        $articles = Cache::remember(
            key: $cacheKey,
            ttl: self::CACHE_TTL,
            callback: fn () => $this->articleIndexPage->index(
                page: $page,
                perPage:  $perPage
            )
        );
        return ArticleResource::collection($articles);
        // 10 - 18 ms
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Article $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $post)
    {
        //
    }
}
