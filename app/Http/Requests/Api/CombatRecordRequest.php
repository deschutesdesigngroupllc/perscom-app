<?php

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class CombatRecordRequest extends Request
{
    /**
     * @return string[]
     */
    public function commonRules(): array
    {
        return [
            'user_id' => 'integer|exists:users,id',
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
            'text' => 'required|string',
        ];
    }
}
