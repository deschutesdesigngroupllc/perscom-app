<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class SlotRequest extends Request
{
    public function commonRules(): array
    {
        return [
            'name' => 'string|max:255',
            'position_id' => 'nullable|integer|exists:positions,id',
            'specialty_id' => 'nullable|integer|exists:specialties,id',
            'description' => 'nullable|string|max:65535',
            'empty' => 'nullable|string|max:65535',
            'order' => 'integer',
            'hidden' => 'boolean',
            'updated_at' => 'date',
            'created_at' => 'date',
        ];
    }

    public function storeRules(): array
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }
}
