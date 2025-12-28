<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $imageableType = fake()->randomElement([Post::class, Comment::class]);

        $filename = Str::random(10) . '.' . fake()->randomElement(['jpg', 'png', 'webp']);

        return [
            'filename' => $filename,
            'path' => 'storage/images/' . $filename,
            'mime_type' => $this->getMimeType($filename),
            'size' => fake()->numberBetween(50000, 2000000), // 50KB - 2MB
            'order' => fake()->numberBetween(0, 5),
            'imageable_id' => $imageableType::factory(),
            'imageable_type' => $imageableType,
        ];
    }
    private function getMimeType(string $filename): string
    {
        return match(pathinfo($filename, PATHINFO_EXTENSION)) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            default => 'image/jpeg',
        };
    }

    // Состояния для конкретных типов
    public function forPost(): static
    {
        return $this->state(fn (array $attributes) => [
            'imageable_id' => Post::factory(),
            'imageable_type' => Post::class,
        ]);
    }

    public function forComment(): static
    {
        return $this->state(fn (array $attributes) => [
            'imageable_id' => Comment::factory(),
            'imageable_type' => Comment::class,
        ]);
    }
}
