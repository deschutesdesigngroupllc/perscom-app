<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class AssignmentRecordRequest extends Request
{
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
            'text' => 'nullable|string',
            'updated_at' => 'date',
            'created_at' => 'date',
            'deleted_at' => 'date',
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

        return $rules;
    }
}
