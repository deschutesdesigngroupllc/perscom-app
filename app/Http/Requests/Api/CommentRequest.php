<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class CommentRequest extends Request
{
    public function commonRules(): array
    {
        return [
            'author_id' => 'integer|exists:users,id',
            'model_type' => 'string|max:255',
            'model_id' => 'integer',
            'comment' => 'string|max:65535',
            'updated_at' => 'date',
            'created_at' => 'date',
        ];
    }

    public function storeRules(): array
    {
        return [
            'author_id' => 'required|integer|exists:users,id',
            'comment' => 'required|string|max:65535',
        ];
    }
}
