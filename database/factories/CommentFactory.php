<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Comment;
use App\Models\ServiceRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    public function configure(): static
    {
        return $this->afterMaking(fn (Comment $comment) => $comment->commentable()->associate(ServiceRecord::factory()->create()));
    }

    public function definition(): array
    {
        return [
            'author_id' => User::factory(),
            'comment' => $this->faker->paragraph,
        ];
    }
}
