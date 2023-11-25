<?php

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class QualificationRecordRequest extends Request
{
    /**
     * @return string[]
     */
    public function commonRules(): array
    {
        return [
            'user_id' => 'integer|exists:users,id',
            'qualification_id' => 'integer|exists:qualifications,id',
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

        return array_merge($rules, [
            'qualification_id' => 'required|integer|exists:qualifications,id',
        ]);
    }
}
