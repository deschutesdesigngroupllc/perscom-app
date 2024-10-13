<?php

declare(strict_types=1);

namespace App\Filament\App\Pages\Auth;

use Filament\Pages\Auth\EmailVerification\EmailVerificationPrompt as BaseEmailVerificationPrompt;

class EmailVerificationPrompt extends BaseEmailVerificationPrompt
{
    protected bool $hasTopbar = false;
}
