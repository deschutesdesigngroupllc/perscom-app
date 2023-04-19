<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Repositories\TenantRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class FindMyOrganizationController extends Controller
{
    /**
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('auth/FindMyOrganization');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, TenantRepository $tenantRepository)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', Rule::exists('tenants', 'email')],
        ], [
            'email.exists' => 'We can\'t find an organization with that email address.',
        ])->validate();

        $tenant = $tenantRepository->findByKey('email', $request->get('email'));

        return redirect()->signedRoute('web.find-my-organization.show', [
            'tenant' => $tenant->id,
        ]);
    }

    /**
     * @return \Inertia\Response
     */
    public function show(Tenant $tenant)
    {
        return Inertia::render('auth/FindMyOrganization', [
            'url' => $tenant->url,
            'tenant' => $tenant->name,
        ]);
    }
}
