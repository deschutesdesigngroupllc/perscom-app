<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

use function in_array;

class SubdomainRule implements ValidationRule
{
    /**
     * @var string[]
     */
    public static array $reservedSubdomains = [
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

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (in_array($value, static::$reservedSubdomains, true)) {
            $fail('The subdomain you entered is protected and may not be used.');
        }
    }
}
