<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class FormRequest extends Request
{
    /**
     * @return string[]
     */
    public function commonRules(): array
    {
        return [
            'name' => 'string|max:255',
            'slug' => 'string|unique:forms,slug|max:255',
            'success_message' => 'nullable|string|max:65535',
            'submission_status_id' => 'nullable|integer|exists:statuses,id',
            'is_public' => 'boolean',
            'description' => 'nullable|string|max:65535',
            'instructions' => 'nullable|string|max:65535',
            'updated_at' => 'date',
            'created_at' => 'date',
            'deleted_at' => 'nullable|date',
        ];
    }

    /**
     * @return string[]
     */
    public function storeRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:forms,slug|max:255',
            'is_public' => 'required|boolean',
        ];
    }
}
