<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\CreateNewTenant;
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
        $createNewTenant->create($request->all());

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
