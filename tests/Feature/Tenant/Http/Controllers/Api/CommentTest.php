<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Http\Controllers\Api\Comments\CommentsController;
use App\Models\Comment;
use App\Models\ServiceRecord;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentTest extends ApiResourceTestCase
{
    public function beforeTestCanReachIndexEndpoint(): void
    {
        $this->user->assignRole(Utils::getSuperAdminName());
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

    /**
     * @return Factory<Comment>
     */
    public function factory(): Factory
    {
        return Comment::factory()
            ->afterMaking(function (Comment $comment) {
                $comment->commentable()->associate(ServiceRecord::factory()->create());
            });
    }

    /**
     * @return string[]
     */
    public function scopes(): array
    {
        return [
            'index' => 'view:comment',
            'show' => 'view:comment',
            'store' => 'create:comment',
            'update' => 'update:comment',
            'delete' => 'delete:comment',
        ];
    }

    /**
     * @return string[]
     */
    public function storeData(): array
    {
        return [
            'author_id' => User::factory()->createQuietly()->getKey(),
            'comment' => $this->faker->paragraph,
        ];
    }

    /**
     * @return string[]
     */
    public function updateData(): array
    {
        return [
            'comment' => $this->faker->paragraph,
        ];
    }
}
