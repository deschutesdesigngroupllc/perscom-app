<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Models\Enums\AssignmentRecordType;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Orion\Http\Requests\Request;

class AssignmentRecordRequest extends Request
{
    /**
     * @return array<string, string|array<string|Enum>>
     */
    public function commonRules(): array
    {
        return [
            'user_id' => 'integer|exists:users,id',
            'status_id' => 'nullable|integer|exists:statuses,id',
            'unit_id' => 'nullable|integer|exists:units,id',
            'position_id' => 'nullable|integer|exists:positions,id',
            'specialty_id' => 'nullable|integer|exists:specialties,id',
            'document_id' => 'nullable|integer|exists:documents,id',
            'author_id' => 'nullable|integer|exists:users,id',
            'type' => [Rule::enum(AssignmentRecordType::class), 'max:255'],
            'text' => 'nullable|string|max:65535',
            'updated_at' => 'date',
            'created_at' => 'date',
            'deleted_at' => 'nullable|date',
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
            'type' => ['required', Rule::enum(AssignmentRecordType::class), 'max:255'],
        ]);
    }
}
