<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\CreateNewTenant;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

        return redirect()->route('register.complete', [
            'id' => $tenant->id,
            'hash' => Hash::make($tenant->email),
        ]);
    }

    /**
     * @param $id
     * @param $hash
     *
     * @return \Illuminate\Http\RedirectResponse|\Inertia\Response
     */
    public function complete($id, $hash)
    {
        $tenant = Tenant::find($id);
        if (!$tenant || !Hash::check($tenant->email, $hash)) {
            return redirect()->route('register.index');
        }

        return Inertia::render('Complete');
    }
}
