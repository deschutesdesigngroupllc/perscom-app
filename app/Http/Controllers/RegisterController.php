<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class RegisterController extends Controller
{
    /**
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Register');
    }

    /**
     * @param  Request  $request
     */
    public function store(Request $request)
    {
        $validated = Request::validate([
            'first_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['required', 'email', 'unique:tenants,email'],
            'password' => ['required'],
            'organization' => ['required', 'unique:tenants,name'],
            'subdomain' => ['required'],
        ]);

        $tenant = Tenant::create([
            'name' => $validated['organization'],
            'email' => $validated['email'],
        ]);

        $domain = $tenant->domains()->create([
            'domain' => $validated['subdomain'] . '.perscom.io',
        ]);

        $tenant->run(function ($tenant) use ($validated) {
            return tap($validated, static function ($values) {
                User::create([
                    'name' => "{$values['first_name']} {$values['last_name']}",
                    'email' => $values['email'],
                    'password' => Hash::make($values['password']),
                ]);
            });
        });

        return redirect()->to(tenant_route($domain->domain, 'nova.pages.home'));
    }
}
