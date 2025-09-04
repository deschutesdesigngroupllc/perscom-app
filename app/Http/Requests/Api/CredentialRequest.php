<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Models\Enums\CredentialType;
use Illuminate\Validation\Rule;
use Orion\Http\Requests\Request;

class CredentialRequest extends Request
{
    public function commonRules(): array
    {
        return [
            'name' => 'string|max:255',
            'description' => 'nullable|string|max:65535',
            'type' => [Rule::enum(CredentialType::class), 'max:255'],
            'issuer_id' => 'integer|exists:issuers,id',
            'order' => 'integer',
            'updated_at' => 'date',
            'created_at' => 'date',
        ];
    }

    public function storeRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::enum(CredentialType::class), 'max:255'],
            'issuer_id' => 'required|integer|exists:issuers,id',
        ];
    }
}
