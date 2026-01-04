<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Article;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 10 пользователей по 2 поста
        User::factory(10)
            ->hasArticles(2)
            ->create();
    }
}
