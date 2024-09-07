<?php

declare(strict_types=1);

namespace App\Http\Controllers\Landing;

use App\Actions\CreateNewTenant;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class RegisterController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Register');
    }

    /**
     * @throws Throwable
     * @throws ValidationException
     */
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
