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
        return Inertia::render('Register');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, CreateNewTenant $createNewTenant)
    {
        $tenant = $createNewTenant->create($request->all());

        return redirect()->signedRoute('web.register.complete', [
            'tenant' => $tenant->id,
        ]);
    }

    /**
     * @return \Inertia\Response
     */
    public function complete(Tenant $tenant)
    {
        return Inertia::render('Complete', [
            'url' => $tenant->url,
        ]);
    }
}
