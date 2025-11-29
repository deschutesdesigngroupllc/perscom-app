<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class AwardRecordRequest extends Request
{
    /**
     * @return array<string, string>
     */
    public function commonRules(): array
    {
        return [
            'user_id' => 'integer|exists:users,id',
            'award_id' => 'integer|exists:awards,id',
            'document_id' => 'nullable|integer|exists:documents,id',
            'author_id' => 'integer|exists:users,id',
            'text' => 'nullable|string|max:65535',
            'updated_at' => 'date',
            'created_at' => 'date',
        ];
    }

    public function storeRules(): array
    {
        $rules = [];
        if (! $this->route('user')) {
            $rules = [
                'user_id' => 'required|integer|exists:users,id',
            ];
        }

        return array_merge($rules, [
            'award_id' => 'required|integer|exists:awards,id',
        ]);
    }
}
