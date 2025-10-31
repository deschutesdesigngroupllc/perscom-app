<?php

declare(strict_types=1);

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\FindMyOrganizationRequest;
use App\Models\Tenant;
use App\Repositories\TenantRepository;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class FindMyOrganizationController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('FindMyOrganization');
    }

    public function store(FindMyOrganizationRequest $request, TenantRepository $tenantRepository): RedirectResponse
    {
        $tenant = $tenantRepository->findByKey('email', $request->validated('email'));

        return redirect()->signedRoute('web.find-my-organization.show', [
            'tenant' => $tenant->getKey(),
        ]);
    }

    public function show(Tenant $tenant): Response
    {
        return Inertia::render('FindMyOrganization', [
            'url' => $tenant->url,
            'tenant' => $tenant->name,
        ]);
    }
}
