<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Models\Enums\RankRecordType;
use Illuminate\Validation\Rule;
use Orion\Http\Requests\Request;

class RankRecordRequest extends Request
{
    public function commonRules(): array
    {
        return [
            'user_id' => 'integer|exists:users,id',
            'rank_id' => 'integer|exists:ranks,id',
            'document_id' => 'nullable|integer|exists:documents,id',
            'author_id' => 'integer|exists:users,id',
            'text' => 'nullable|string|max:65535',
            'type' => Rule::enum(RankRecordType::class),
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
            'rank_id' => 'required|integer|exists:ranks,id',
            'type' => ['required', Rule::enum(RankRecordType::class)],
        ]);
    }
}
