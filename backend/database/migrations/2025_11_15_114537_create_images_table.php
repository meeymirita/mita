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
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('path');
            $table->string('mime_type')->nullable();
            $table->integer('size')->nullable();
            $table->integer('order')->default(0);

            // Полиморфные поля
            $table->unsignedBigInteger('imageable_id');
            $table->string('imageable_type'); // Тип сущности (App\Models\Post или App\Models\Comment)

            $table->timestamps();

            // Индекс для полиморфной связи
            $table->index(['imageable_type', 'imageable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
