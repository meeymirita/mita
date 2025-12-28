<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class TagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true); // Два слова для тега

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'color' => fake()->randomElement(['#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6']),
            'description' => fake()->boolean(70) ? fake()->sentence() : null, // 70% имеют описание
        ];
    }
}
