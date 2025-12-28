<?php

namespace App\Http\Controllers\Post;

use App\Contracts\PostInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Post\StoreRequest;
use App\Http\Resources\Post\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PostController extends Controller
{
    public $postService;

    /**
     * @param PostInterface $postService
     */
    public function __construct(PostInterface $postService){
        $this->postService = $postService;
    }
    // Все посты всех пользователей на главную

    /**
     * @return AnonymousResourceCollection
     */
    public function index() : AnonymousResourceCollection
    {
        return PostResource::collection(
            Post::query()->with(['images', 'tags'])
                ->latest()->paginate(2)
        );
    }

    /**
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $post = $this->postService->store($request->validated());
        return response()->json([
            'message' => 'Post created successfully',
            'post' => new PostResource($post)
        ], 201);
    }
    // просмотр одного поста
    /**
     * @param Post $post
     * @return PostResource
     */
    public function show(Post $post): PostResource
    {
        return new PostResource($post);
    }
    public function update(Request $request, Post $post)
    {
        //
    }
    public function destroy(Post $post)
    {
        //
    }
}
