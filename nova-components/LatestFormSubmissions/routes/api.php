<?php

use App\Models\Forms\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Nova\Http\Middleware\Authenticate;
use Laravel\Nova\Http\Middleware\Authorize;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;

/*
|--------------------------------------------------------------------------
| Card API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your card. These routes
| are loaded by the ServiceProvider of your card. You're free to add
| as many additional routes to this file as your card may require.
|
*/

 Route::get('/all', function (Request $request) {
     return Submission::query()->orderBy('created_at')->limit(10)->get()->map(function ($submission) {
     	return [
     		'id' => $submission->id,
	        'user' => $submission->user->name,
	        'date' => \Illuminate\Support\Carbon::parse($submission->created_at)->toDayDateTimeString(),
	        'form' => $submission->form->name,
	        'view_url' => \route('nova.pages.detail', [
	        	'resource' => 'submissions',
		        'resourceId' => $submission->id
	        ])
        ];
     });
 })->middleware([Authenticate::class, Authorize::class]);
