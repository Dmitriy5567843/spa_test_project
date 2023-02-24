<?php

namespace Database\Factories;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $comment = Comment::inRandomOrder()->first();



        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->email,
            'content' => $this->faker->realText,
        ];
    }
}
