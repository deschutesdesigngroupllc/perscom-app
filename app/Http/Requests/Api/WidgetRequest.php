<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class WidgetRequest extends FormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'widget' => ['in:awards,calendar,forms,positions,qualifications,ranks,roster,specialties'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'widget.in' => 'The requested :attribute is invalid. Please provide a valid widget.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'widget' => $this->route('widget'),
        ]);
    }
}
