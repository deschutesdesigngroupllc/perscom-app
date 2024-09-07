<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Validation\Rules\Password;

trait PasswordValidationRules
{
    /**
     * @return array<int, mixed>
     */
    protected function passwordRules(): array
    {
        return ['required', 'string', new Password(8), 'confirmed'];
    }
}
