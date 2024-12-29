<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class StatusRequest extends Request
{
    /**
     * @return array<string, string|array<string>>
     */
    public function commonRules(): array
    {
        return [
            'name' => 'string|max:255',
            'color' => ['string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', 'max:255'],
            'order' => 'integer',
            'updated_at' => 'date',
            'created_at' => 'date',
        ];
    }

    /**
     * @return array<string, string|array<string>>
     */
    public function storeRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', 'max:255'],
        ];
    }
}
