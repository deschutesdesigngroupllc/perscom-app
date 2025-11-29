<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Comments;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\CommentRequest;
use App\Models\Comment;
use Orion\Http\Controllers\Controller;

class CommentsController extends Controller
{
    use AuthorizesRequests;

    protected $model = Comment::class;

    protected $request = CommentRequest::class;

    /**
     * @return array<int, string>
     */
    public function includes(): array
    {
        return ['author', 'commentable'];
    }

    /**
     * @return array<int, string>
     */
    public function sortableBy(): array
    {
        return ['id', 'author_id', 'author.*', 'model_id', 'model_type', 'commentable.*', 'comment', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function searchableBy(): array
    {
        return ['id', 'author_id', 'model_id', 'model_type', 'comment', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function filterableBy(): array
    {
        return ['id', 'author_id', 'author.*', 'model_id', 'model_type', 'commentable.*', 'comment', 'created_at', 'updated_at'];
    }
}
