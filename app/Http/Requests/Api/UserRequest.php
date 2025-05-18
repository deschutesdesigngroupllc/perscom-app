<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Models\User;
use App\Services\FieldService;
use Orion\Http\Requests\Request;

class UserRequest extends Request
{
    /**
     * @return string[]
     */
    public function commonRules(): array
    {
        return array_merge([
            'name' => 'string|max:255',
            'email' => 'email|unique:users,email|max:255',
            'email_verified_at' => 'nullable|date',
            'position_id' => 'nullable|integer|exists:positions,id',
            'rank_id' => 'nullable|integer|exists:ranks,id',
            'specialty_id' => 'nullable|integer|exists:specialties,id',
            'status_id' => 'nullable|integer|exists:statuses,id',
            'unit_id' => 'nullable|integer|exists:units,id',
            'approved' => 'boolean',
            'notes' => 'nullable|string',
            'notes_updated_at' => 'nullable|date',
            'profile_photo' => 'nullable|image|min:1|max:10000',
            'cover_photo' => 'nullable|image|min:1|max:10000',
            'last_seen_at' => 'nullable|date',
            'facebook_user_id' => 'nullable|string',
            'github_user_id' => 'nullable|string',
            'google_user_id' => 'nullable|string',
            'discord_user_id' => 'nullable|string',
            'updated_at' => 'date',
            'created_at' => 'date',
        ], $this->getFieldRules());
    }

    /**
     * @return string[]
     */
    public function storeRules(): array
    {
        return array_merge([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
        ], $this->getFieldRules());
    }

    /**
     * @return string[]
     */
    protected function getFieldRules(): array
    {
        $user = $this->route('user');

        if (is_null($user)) {
            return [];
        }

        return FieldService::getValidationRules(User::findOrFail($user)->fields)->toArray();
    }
}
