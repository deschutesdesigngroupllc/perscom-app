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
            'unit_id' => 'integer|exists:units,id',
            'position_id' => 'integer|exists:positions,id',
            'specialty_id' => 'integer|exists:specialties,id',
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
        return [
            'user_id' => 'required|integer|exists:users,id',
            'unit_id' => 'required|integer|exists:units,id',
            'position_id' => 'required|integer|exists:positions,id',
            'specialty_id' => 'required|integer|exists:specialties,id',
            'text' => 'required|string',
        ];
    }
}
