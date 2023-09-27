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
            'text' => 'string',
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
            'text' => 'required|string',
        ]);
    }
}
