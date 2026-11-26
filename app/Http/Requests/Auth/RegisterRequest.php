<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Models\Enums\OrganizationType;
use App\Models\Registration;
use App\Models\Tenant;
use App\Rules\SubdomainRule;
use App\Rules\TurnstileRule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $rules = [
            'organization' => ['required', 'string', 'max:255', Rule::unique(Tenant::class, 'name')],
            'name' => ['required', 'string', 'max:255'],
            'subdomain' => [
                'nullable',
                'string',
                'min:3',
                'max:63',
                'regex:/^[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/',
                Rule::unique('mysql.domains', 'domain'),
                Rule::unique(Registration::class, 'subdomain')
                    ->where(fn (Builder $query): Builder => $query
                        ->where('email', '!=', (string) $this->input('email'))),
                new SubdomainRule,
            ],
            'organization_type' => ['nullable', Rule::enum(OrganizationType::class)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(Tenant::class, 'email')],
            'privacy' => ['required', 'boolean'],
        ];

        if (filled(config('services.cloudflare.turnstile.site_key'))) {
            $rules['token'] = ['bail', 'required', 'string', new TurnstileRule];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'subdomain.regex' => 'Use lowercase letters, numbers and dashes, starting and ending with a letter or number.',
            'privacy.required' => 'You must agree to the policies.',
            'token.required' => 'Please complete the CAPTCHA to continue.',
        ];
    }
}
