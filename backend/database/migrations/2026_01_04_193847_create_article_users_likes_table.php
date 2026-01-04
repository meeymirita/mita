<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles_users_likes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('articles_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->index('articles_id');
            $table->index('user_id');

            $table->foreign('articles_id')->references('id')->on('articles');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles_users_likes');
    }
};
