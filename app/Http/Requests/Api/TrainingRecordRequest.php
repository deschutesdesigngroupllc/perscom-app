<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Validation\Rules\Enum;
use Orion\Http\Requests\Request;

class TrainingRecordRequest extends Request
{
    /**
     * @return array<string, string|Enum>
     */
    public function commonRules(): array
    {
        return [
            'user_id' => 'integer|exists:users,id',
            'instructor_id' => 'integer|exists:users,id',
            'document_id' => 'nullable|integer|exists:documents,id',
            'author_id' => 'nullable|integer|exists:users,id',
            'text' => 'nullable|string|max:65535',
            'updated_at' => 'date',
            'created_at' => 'date',
        ];
    }

    /**
     * @return array<string, string|array<string|Enum>>
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
            'instructor_id' => 'required|integer|exists:users,id',
        ]);
    }
}
