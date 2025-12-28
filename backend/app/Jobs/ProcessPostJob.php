<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use App\Models\Post;

class ProcessPostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public $action;
    public $data;

    /**
     * @param string $action
     * @param $data
     * @param string $queue
     */
    public function __construct(string $action, $data, string $queue)
    {
        $this->action = $action;
        $this->data = $data;

        $this->onConnection('rabbitmq');
        $this->onQueue($queue);
    }

    /**
     * @return void
     */
    public function handle()
    {
        Log::info("🎯 Processing Post Job", [
            'action' => $this->action,
            'queue' => $this->queue,
            'data_type' => gettype($this->data)
        ]);

        switch ($this->action) {
            case 'post_created':
                $this->handlePostCreated($this->data);
                break;

            default:
                Log::warning("❌ Unknown post action: {$this->action}");
        }
    }

    /**
     * @param $data
     * @return void
     */
    protected function handlePostCreated($data)
    {
        if ($data instanceof Post) {
            $post = $data;
            Log::info("📝 Post created event processed (Model)", [
                'post_id' => $post->id,
                'title' => $post->title,
                'user_id' => $post->user_id
            ]);
        } elseif (is_array($data) && isset($data['id'])) {
            // Если передан массив
            $post = Post::find($data['id']);
            if (!$post) {
                Log::error("❌ Post not found for creation", ['data' => $data]);
                return;
            }
            Log::info("📝 Post created event processed (Array)", [
                'post_id' => $post->id,
                'title' => $post->title,
                'user_id' => $post->user_id
            ]);
        } else {
            Log::error("❌ Invalid data type for post creation", [
                'data_type' => gettype($data),
                'data' => $data
            ]);
            return;
        }
        $this->processPostCreation($post);
    }

    protected function processPostCreation(Post $post)
    {
        // 🎯 ЗДЕСЬ ВСЯ ЛОГИКА ОБРАБОТКИ:

        // 1. Отправка уведомлений подписчикам
        // Notification::send($post->user->followers, new NewPostNotification($post));

        // 2. Индексация в поиске
        // $post->searchable();

        // 3. Обновление кэша
        // Cache::forget('recent_posts');

        // 4. Аналитика
        // Analytics::track('Post Created', ['post_id' => $post->id]);

        // 5. Генерация превью
        // $this->generatePreview($post);

        // 6. Проверка на спам
        // $this->checkForSpam($post);

        Log::info("✅ Post processing completed", ['post_id' => $post->id]);
    }
}
