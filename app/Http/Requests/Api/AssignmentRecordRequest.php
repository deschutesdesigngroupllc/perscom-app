<?php

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class AssignmentRecordRequest extends Request
{
    /**
     * @return string[]
     */
    public function commonRules(): array
    {
        return [
            'user_id' => 'integer|exists:users,id',
            'status_id' => 'nullable|integer|exists:statuses,id',
            'unit_id' => 'nullable|integer|exists:units,id',
            'secondary_unit_ids' => 'nullable|array',
            'position_id' => 'nullable|integer|exists:positions,id',
            'secondary_position_ids' => 'nullable|array',
            'specialty_id' => 'nullable|integer|exists:specialties,id',
            'secondary_specialty_ids' => 'nullable|array',
            'document_id' => 'nullable|integer|exists:documents,id',
            'author_id' => 'nullable|integer|exists:users,id',
            'text' => 'nullable|string',
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

        return $rules;
    }
}
