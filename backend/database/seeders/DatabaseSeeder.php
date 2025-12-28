<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Image;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::factory(1000)->create();

        $tags = Tag::factory(2000)->create();

        $posts = Post::factory(2000)
            ->recycle($users)
            ->create()
            ->each(function ($post) use ($tags) {
                $post->tags()->attach(
                    $tags->random(random_int(1, 4))->pluck('id')->toArray()
                );
            });

        $comments = Comment::factory(2000)
            ->recycle($users)
            ->recycle($posts)
            ->create();

        Image::factory(210)->forPost()->recycle($posts)->create();
        Image::factory(310)->forComment()->recycle($comments)->create();
    }
}
