<?php

declare(strict_types=1);

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Registration;
use App\Notifications\Tenant\RegistrationVerificationRequired;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RegisterController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $registration = Registration::create([
            'organization' => $request->validated('organization'),
            'email' => $request->validated('email'),
        ]);

        $registration->notify(new RegistrationVerificationRequired($registration));

        return redirect()->signedRoute('web.register.show');
    }

    public function show(): Response
    {
        return Inertia::render('Verify');
    }
}
