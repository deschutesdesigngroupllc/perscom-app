<?php

namespace App\Http\Controllers\Login\Social;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RedirectController extends Controller
{
    public function index(string $driver, string $function): RedirectResponse
    {
        return redirect()->route('auth.social.redirect', [
            'driver' => $driver,
            'function' => $function,
            'tenant' => tenant()->getTenantKey(),
        ]);
    }
}
