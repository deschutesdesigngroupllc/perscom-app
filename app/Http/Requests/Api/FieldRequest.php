<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Models\Enums\FieldOptionsModel;
use App\Models\Enums\FieldOptionsType;
use App\Models\Enums\FieldType;
use Illuminate\Validation\Rule;
use Orion\Http\Requests\Request;

class FieldRequest extends Request
{
    /**
     * @return string[]
     */
    public function commonRules(): array
    {
        return [
            'name' => 'string|max:255',
            'key' => 'string|regex:/^(?!.*__)[a-z0-9]+(?:_[a-z0-9]+)*$/i|unique:fields,key|max:255',
            'type' => [Rule::enum(FieldType::class)],
            'cast' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:65535',
            'placeholder' => 'nullable|string|max:65535',
            'default' => 'nullable|string|max:255',
            'help' => 'nullable|string|max:65535',
            'required' => 'boolean',
            'rules' => 'nullable|string|max:255',
            'readonly' => 'boolean',
            'hidden' => 'boolean',
            'options' => 'json',
            'options_type' => [Rule::enum(FieldOptionsType::class)],
            'options_model' => [Rule::enum(FieldOptionsModel::class)],
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
            'name' => 'required|string|max:255',
            'key' => 'required|regex:/^(?!.*__)[a-z0-9]+(?:_[a-z0-9]+)*$/i|string|unique:fields,key|max:255',
            'type' => ['required', Rule::enum(FieldType::class)],
        ];
    }
}
