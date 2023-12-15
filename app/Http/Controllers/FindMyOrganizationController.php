<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Repositories\TenantRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class FindMyOrganizationController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('auth/FindMyOrganization');
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request, TenantRepository $tenantRepository): RedirectResponse
    {
        Validator::make($request->all(), [
            'email' => ['required', 'email', Rule::exists('tenants', 'email')],
        ], [
            'email.exists' => 'We can\'t find an organization with that email address.',
        ])->validate();

        $tenant = $tenantRepository->findByKey('email', $request->get('email'));

        return redirect()->signedRoute('web.find-my-organization.show', [
            'tenant' => $tenant->id,
        ]);
    }

    public function show(Tenant $tenant): Response
    {
        return Inertia::render('auth/FindMyOrganization', [
            'url' => $tenant->url,
            'tenant' => $tenant->name,
        ]);
    }
}
