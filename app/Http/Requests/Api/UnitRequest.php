<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Models\Unit;
use App\Rules\UnitParentRule;
use Orion\Http\Requests\Request;

class UnitRequest extends Request
{
    /**
     * @return array<string, string|array<int, mixed>>
     */
    public function commonRules(): array
    {
        $unit = $this->route('unit');

        return [
            'parent_id' => ['nullable', 'integer', 'exists:units,id', new UnitParentRule(filled($unit) ? Unit::query()->find($unit) : null)],
            'name' => 'string|max:255',
            'description' => 'nullable|string|max:65535',
            'order' => 'integer',
            'hidden' => 'boolean',
            'icon' => 'nullable|string|max:255',
            'updated_at' => 'date',
            'created_at' => 'date',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function storeRules(): array
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }
}
