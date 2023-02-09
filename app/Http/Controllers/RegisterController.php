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
     * @param  Request  $request
     * @param  CreateNewTenant  $createNewTenant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, CreateNewTenant $createNewTenant)
    {
        $tenant = $createNewTenant->create($request->all());

        return redirect()->signedRoute('web.register.complete', [
            'id' => $tenant->id,
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Inertia\Response
     */
    public function complete($id)
    {
        $tenant = Tenant::find($id);
        if (! $tenant) {
            return redirect()->route('web.register.index');
        }

        return Inertia::render('Complete', [
            'url' => $tenant->url,
        ]);
    }
}
