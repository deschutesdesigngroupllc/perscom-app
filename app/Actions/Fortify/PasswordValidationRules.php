<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Rules\Password;

trait PasswordValidationRules
{
    /**
     * @return array<string|mixed>
     */
    protected function passwordRules(): array
    {
        return ['required', 'string', new Password(), 'confirmed'];
    }
}
