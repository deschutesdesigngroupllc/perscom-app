<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FindMyOrganizationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', Rule::exists('tenants', 'email')],
        ];
    }

    public function messages(): array
    {
        return [
            'email.exists' => 'We can\'t find an organization with that email address.',
        ];
    }
}
