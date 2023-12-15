<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\CreateNewTenant;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RegisterController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Register');
    }

    public function store(Request $request, CreateNewTenant $createNewTenant): RedirectResponse
    {
        $tenant = $createNewTenant->create($request->all());

        return redirect()->signedRoute('web.register.complete', [
            'tenant' => $tenant->id,
        ]);
    }

    public function complete(Tenant $tenant): Response
    {
        return Inertia::render('Complete', [
            'url' => $tenant->url,
        ]);
    }
}
