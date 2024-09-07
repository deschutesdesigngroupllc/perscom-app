<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Http\Controllers\Api\Comments\CommentsController;
use App\Models\Comment;
use App\Models\ServiceRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentTest extends ApiResourceTestCase
{
    public function beforeTestCanReachIndexEndpoint(): void
    {
        $this->user->assignRole('Admin');
    }

    public function endpoint(): string
    {
        return 'comments';
    }

    public function model(): string
    {
        return Comment::class;
    }

    public function controller(): string
    {
        return CommentsController::class;
    }

    public function factory(): Factory
    {
        return Comment::factory()
            ->afterMaking(function (Comment $comment) {
                $comment->commentable()->associate(ServiceRecord::factory()->create());
            });
    }

    public function scopes(): array
    {
        return [
            'index' => 'view:servicerecord',
            'show' => 'view:servicerecord',
            'store' => 'create:comment',
            'update' => 'update:servicerecord',
            'delete' => 'delete:servicerecord',
        ];
    }

    public function storeData(): array
    {
        return [
            'author_id' => User::factory()->create()->getKey(),
            'comment' => $this->faker->paragraph,
        ];
    }

    public function updateData(): array
    {
        return [
            'comment' => $this->faker->paragraph,
        ];
    }
}
