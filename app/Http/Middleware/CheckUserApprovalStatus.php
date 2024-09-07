<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use App\Settings\RegistrationSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserApprovalStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var RegistrationSettings $settings */
        $settings = app(RegistrationSettings::class);

        /** @var User|null $user */
        $user = $request->user();

        abort_if(
            $user && ! $user->approved && $settings->admin_approval_required,
            401,
            'Your account is awaiting approval by an administrator. Please try again later.'
        );

        return $next($request);
    }
}
