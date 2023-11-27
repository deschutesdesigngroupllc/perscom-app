<?php

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class RankRecordRequest extends Request
{
    /**
     * @return string[]
     */
    public function commonRules(): array
    {
        return [
            'user_id' => 'integer|exists:users,id',
            'rank_id' => 'integer|exists:ranks,id',
            'document_id' => 'nullable|integer|exists:documents,id',
            'author_id' => 'nullable|integer|exists:users,id',
            'text' => 'nullable|string',
            'type' => 'integer|in:0,1',
            'updated_at' => 'date',
            'created_at' => 'date',
        ];
    }

    /**
     * @return string[]
     */
    public function storeRules(): array
    {
        $rules = [];
        if (! $this->route('user')) {
            $rules = [
                'user_id' => 'required|integer|exists:users,id',
            ];
        }

        return array_merge($rules, [
            'rank_id' => 'required|integer|exists:ranks,id',
            'type' => 'required|integer|in:0,1',
        ]);
    }
}
