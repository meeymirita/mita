<?php

namespace Database\Seeders;

use App\Models\ArticleLike;
use App\Models\User;
use App\Models\Article;
use Illuminate\Database\Seeder;
use Random\RandomException;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 10 пользователей по 2 поста
        $users = User::factory(20)
            ->hasArticles(2)
            ->create();

        $articles = Article::all();

        foreach ($articles as $article) {
            $randomUsers = $users->random(rand(1, 10));
            foreach ($randomUsers as $user) {
                ArticleLike::create([
                    'article_id' => $article->id,
                    'user_id' => $user->id
                ]);
            }
        }
    }
}
