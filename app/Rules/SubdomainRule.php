<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SubdomainRule implements ValidationRule
{
    /**
     * @var string[]
     */
    protected static $reservedSubdomains = [
        'admin',
        'api',
        'app',
        'assets',
        'auth',
        'billing',
        'bounce',
        'docs',
        'horizon',
        'mail',
        'origin',
        'staging',
        'status',
        'telescope',
        'widget',
    ];

    /**
     * Run the validation rule.
     *f
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (\in_array($value, static::$reservedSubdomains, true)) {
            $fail('The subdomain you entered is protected and may not be used.');
        }
    }
}
