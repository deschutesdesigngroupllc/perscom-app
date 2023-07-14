<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckUserApprovalStatus
{
    public function handle(Request $request, Closure $next): Response|RedirectResponse|JsonResponse
    {
        if (! $request->user()?->approved && setting('registration_admin_approval_required', false)) {
            abort(401, 'Your account is awaiting approval by an administrator. Please try again later.');
        }

        return $next($request);
    }
}
