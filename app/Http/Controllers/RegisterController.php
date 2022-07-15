<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\CreateNewTenant;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Inertia\Inertia;
use Stancl\Tenancy\Events\TenantCreated;

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
     * @param  Request          $request
     * @param  CreateNewTenant  $createNewTenant
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, CreateNewTenant $createNewTenant)
    {
        $tenant = Tenant::withoutEvents(static function () use ($createNewTenant, $request) {
            return $createNewTenant->create($request->all());
        });

        Event::dispatch(new TenantCreated($tenant));

        return redirect()->route('register.complete');
    }

    /**
     * @return \Inertia\Response
     */
    public function complete()
    {
        return Inertia::render('Complete');
    }
}
