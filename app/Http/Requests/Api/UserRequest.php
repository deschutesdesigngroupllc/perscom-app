<?php

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class UserRequest extends Request
{
    /**
     * @return string[]
     */
    public function commonRules(): array
    {
        return [
            'name' => 'string',
            'email' => 'email|unique:users,email',
            'email_verified_at' => 'nullable|date',
            'position_id' => 'nullable|integer|exists:positions,id',
            'rank_id' => 'nullable|integer|exists:ranks,id',
            'specialty_id' => 'nullable|integer|exists:specialties,id',
            'status_id' => 'nullable|integer|exists:statuses,id',
            'unit_id' => 'nullable|integer|exists:units,id',
            'approved' => 'boolean',
            'notes' => 'nullable|string',
            'notes_updated_at' => 'nullable|date',
            'profile_photo' => 'nullable|string',
            'cover_photo' => 'nullable|string',
            'last_seen_at' => 'nullable|date',
            'updated_at' => 'date',
            'created_at' => 'date',
        ];
    }

    /**
     * @return string[]
     */
    public function storeRules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
        ];
    }
}
