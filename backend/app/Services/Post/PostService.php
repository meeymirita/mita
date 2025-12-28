<?php

namespace App\Services\Post;

use App\Contracts\PostInterface;
use App\Enums\ColorTag;
use App\Models\Post;
use App\Models\Tag;
use App\Services\RabbitMQ\RabbitMQService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Jobs\ProcessPostJob;

class PostService implements PostInterface
{
    /**
     * @var RabbitMQService
     */
    protected $rabbitMQService;

    /**
     * @param RabbitMQService $rabbitMQService
     */
    public function __construct(RabbitMQService $rabbitMQService)
    {
        $this->rabbitMQService = $rabbitMQService;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function store(array $data)
    {
        // пост
        $post = Post::create([
            'user_id' => auth()->id(),
            'title' => $data['title'],
            'content' => $data['content'],
        ]);
        // теги
        if (isset($data['tags'])) {
            foreach ($data['tags'] as $tagName) {
                $tag = Tag::firstOrCreate(
                    ['name' => $tagName],
                    [
                        'slug' => Str::slug($tagName),
                        'color' => ColorTag::random()
                    ]
                );
                $post->tags()->attach($tag->id);
            }
        }
        // изображения
        if (isset($data['images'])) {
            foreach ($data['images'] as $image) {
                $path = $image->store('posts', 'public');

                $post->images()->create([
                    'filename' => $image->getClientOriginalName(),
                    'path' => $path,
                    'mime_type' => $image->getMimeType(),
                    'size' => $image->getSize(),
                    'order' => 0
                ]);
            }
        }

        $post->load(['tags', 'images', 'user']);
        // отправка в очередь -> передавать можно любую ребит под капотом сам создас
        $this->sendPostCreatedEvent($post, 'user_post_created');

        return $post;
    }

    /**
     * Отправка события создания поста в RabbitMQ
     */
    protected function sendPostCreatedEvent(Post $post, string $queue)
    {
        try {
            ProcessPostJob::dispatch(action: 'post_created', data: $post, queue: $queue);
            Log::info("📨 Post creation event dispatched to queue: {$queue}", [
                'post_id' => $post->id,
                'queue' => $queue
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send post created event: ' . $e->getMessage());
        }
    }

    public function destroy(array $data)
    {
        // TODO: Implement destroy() method.
    }

    public function update(array $data)
    {
        // TODO: Implement update() method.
    }
}
