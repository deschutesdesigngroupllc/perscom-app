<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\CreateNewTenant;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Spatie\Url\Url;
use Stancl\Tenancy\Events\DatabaseCreated;

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
	 * @param  CreatesNewUsers  $createsNewUsers
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
    public function store(Request $request, CreateNewTenant $createNewTenant, CreatesNewUsers $createsNewUsers)
    {
    	$tenant = $createNewTenant->create($request->all());

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
