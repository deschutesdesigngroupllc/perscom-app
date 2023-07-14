<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserApprovalStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->approved && setting('registration_admin_approval_required', false)) {
            abort(401, 'Your account is awaiting approval by an administrator. Please try again later.');
        }

        return $next($request);
    }
}
