<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use App\Settings\RegistrationSettings;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserApprovalStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var ?User $user */
        $user = Auth::user();

        if (! $user || $user->approved || $request->routeIs('*.logout')) {
            return $next($request);
        }

        /** @var RegistrationSettings $settings */
        $settings = app(RegistrationSettings::class);

        if (! $settings->admin_approval_required) {
            return $next($request);
        }

        abort_if(
            $request->expectsJson(),
            401,
            'Your account is awaiting approval by an administrator. Please try again later.'
        );

        return redirect()->route('tenant.approval-required');
    }
}
