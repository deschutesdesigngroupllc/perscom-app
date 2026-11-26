<?php

declare(strict_types=1);

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Enums\OrganizationType;
use App\Models\Registration;
use App\Notifications\Tenant\RegistrationVerificationRequired;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class RegisterController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Register', [
            'organizationTypes' => collect(OrganizationType::cases())->map(fn (OrganizationType $type): array => ['value' => $type->value, 'label' => $type->getLabel()])->all(),
            'baseDomain' => parse_url((string) config('app.url'), PHP_URL_HOST),
        ]);
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $registration = Registration::query()->updateOrCreate([
            'email' => $request->validated('email'),
            'verified_at' => null,
        ], [
            'organization' => $request->validated('organization'),
            'name' => $request->validated('name'),
            'subdomain' => filled($request->validated('subdomain')) ? Str::lower((string) $request->validated('subdomain')) : null,
            'organization_type' => $request->validated('organization_type'),
        ]);

        $registration->notify(new RegistrationVerificationRequired($registration));

        return redirect()->signedRoute('web.register.show');
    }

    public function show(): Response
    {
        return Inertia::render('Verify');
    }
}
