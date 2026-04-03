<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Models\Registration;
use App\Models\Tenant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class RegisterRequest extends FormRequest
{
    /**
     * @return array<string, Unique[]|string[]>
     */
    public function rules(): array
    {
        $rules = [
            'organization' => ['required', 'string', 'max:255', Rule::unique(Tenant::class, 'name')],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(Tenant::class, 'email'), Rule::unique(Registration::class, 'email')],
            'privacy' => ['required', 'boolean'],
        ];

        if (filled(config('services.cloudflare.turnstile.site_key'))) {
            $rules['token'] = ['required', 'string'];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'privacy.required' => 'You must agree to the policies.',
            'token.required' => 'Please complete the CAPTCHA to continue.',
        ];
    }
}
