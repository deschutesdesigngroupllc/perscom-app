<?php

namespace App\Http\Requests;

use Orion\Http\Requests\Request;

class UserRequest extends Request
{
    /**
     * @return string[]
     */
    public function commonRules(): array
    {
        return [
            'email_verified_at' => 'nullable|date',
            'notes_updated_at' => 'nullable|date',
            'last_seen_at' => 'nullable|date',
        ];
    }

    /**
     * @return string[]
     */
    public function storeRules(): array
    {
        return [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
        ];
    }
}
