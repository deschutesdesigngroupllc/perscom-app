<?php

declare(strict_types=1);

namespace App\Filament\App\Pages\Auth;

class EmailVerificationPrompt extends \Filament\Auth\Pages\EmailVerification\EmailVerificationPrompt
{
    protected bool $hasTopbar = false;
}
