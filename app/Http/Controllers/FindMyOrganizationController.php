<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
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
     * @param  Request  $request
     * @return \Inertia\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['email', Rule::exists('tenants', 'email')],
        ], [
            'email.exists' => 'We can\'t find an organization with that email address.',
        ])->validate();

        $tenant = Tenant::where('email', '=', $request->get('email'))->firstOrFail();

        return redirect()->signedRoute('web.find-my-organization.show', [
            'tenant' => $tenant,
        ]);
    }

    /**
     * @param  Tenant  $tenant
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
