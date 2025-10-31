<?php

declare(strict_types=1);

namespace App\Http\Controllers\Landing;

use App\Actions\Tenant\CreateNewTenant;
use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VerifyController extends Controller
{
    public function __invoke(Request $request, Registration $registration, CreateNewTenant $createNewTenant): Response
    {
        if ($registration->verified_at === null) {
            $tenant = $createNewTenant->create(
                organization: $registration->organization,
                email: $registration->email
            );

            $registration->update([
                'verified_at' => now(),
                'tenant_id' => $tenant->id,
            ]);
        }

        $tenant = $registration->tenant;

        return Inertia::render('Complete', [
            'url' => $tenant?->url,
        ]);
    }
}
