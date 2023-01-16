<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\CreateNewTenant;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RegisterController extends Controller
{
    /**
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Register', [
            'baseUrl' => config('app.base_url'),
        ]);
    }

    /**
     * @param  Request  $request
     * @param  CreateNewTenant  $createNewTenant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, CreateNewTenant $createNewTenant)
    {
        $tenant = $createNewTenant->create($request->all());

        return redirect()->route('register.complete', [
            'tenant' => $tenant,
        ]);
    }

    /**
     * @param $tenant
     * @return \Inertia\Response|void
     */
    public function complete($tenant)
    {
        $tenant = Tenant::find($tenant);
        if (! $tenant || ! $tenant->wasRecentlyCreated) {
            return redirect()->route('register.index');
        }

        return Inertia::render('Complete');
    }
}
