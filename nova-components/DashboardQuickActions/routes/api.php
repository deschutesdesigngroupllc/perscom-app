<?php

use App\Nova\AssignmentRecord;
use App\Nova\AwardRecord;
use App\Nova\CombatRecord;
use App\Nova\Form;
use App\Nova\RankRecord;
use App\Nova\ServiceRecord;
use App\Nova\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Laravel\Nova\Http\Middleware\Authenticate;
use Laravel\Nova\Http\Middleware\Authorize;

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

Route::get('/routes', function (Request $request) {
    $routes = [];

    $routes['user']['personnefile'] = [
        'link' => \route('nova.pages.detail', [
            'resource' => User::uriKey(),
            'resourceId' => Auth::user()->getAuthIdentifier(),
        ]),
        'title' => 'View My Personnel Profile',
        'description' => 'View my personal information and perform an updates that may be needed.',
        'icon' => 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z',
    ];

    $routes['user']['formsubmission'] = [
        'link' => \route('nova.pages.index', [
            'resource' => Form::uriKey()
        ]),
        'title' => 'Submit A New Form',
        'description' => 'Submit a new form or perform a request using your organization\'s forms.',
        'icon' => 'M12 10.5v6m3-3H9m4.06-7.19l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z',
    ];

    if (Gate::check('create', \App\Models\User::class)) {
        $routes['admin']['user'] = [
            'link' => \route('nova.pages.create', [
                'resource' => User::uriKey()
            ]),
            'title' => 'Create A New Personnel Profile',
            'description' => 'Personnel profiles are the foundation of PERSCOM and provide a powerful and robust interface to store data while being displayed in a elegant and useable fashion.',
            'icon' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
        ];
    }

    if (Gate::check('create', \App\Models\AssignmentRecord::class)) {
        $routes['admin']['assignment'] = [
            'link' => \route('nova.pages.create', [
                'resource' => AssignmentRecord::uriKey()
            ]),
            'title' => 'Register A New Assignment',
            'description' => 'Assignment records provide a transparent ledger for all personnel transfers that happen within your organization.',
            'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4',
        ];
    }

    if (Gate::check('create', \App\Models\ServiceRecord::class)) {
        $routes['admin']['service'] = [
            'link' => \route('nova.pages.create', [
                'resource' => ServiceRecord::uriKey()
            ]),
            'title' => 'Create A New Service Record',
            'description' => 'Service records provide a written history of every action and event that happens to a user. Records can be manually generated or automated through commmon actions.',
            'icon' => 'M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        ];
    }

    if (Gate::check('create', \App\Models\CombatRecord::class)) {
        $routes['admin']['combat'] = [
            'link' => \route('nova.pages.create', [
                'resource' => CombatRecord::uriKey()
            ]),
            'title' => 'Create A New Combat Record',
            'description' => 'Similiar to Service Records, Combat Records provide a timeline of all combat encounters by the user.',
            'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        ];
    }

    if (Gate::check('create', \App\Models\RankRecord::class)) {
        $routes['admin']['rank'] = [
            'link' => \route('nova.pages.create', [
                'resource' => RankRecord::uriKey()
            ]),
            'title' => 'File A New Promotion',
            'description' => 'Assign a new rank to a user, making sure to keep a thorough and detailed history of all changes in rank for the user.',
            'icon' => 'M9 11l3-3m0 0l3 3m-3-3v8m0-13a9 9 0 110 18 9 9 0 010-18z',
        ];
    }

    if (Gate::check('create', \App\Models\AwardRecord::class)) {
        $routes['admin']['award'] = [
            'link' => \route('nova.pages.create', [
                'resource' => AwardRecord::uriKey()
            ]),
            'title' => 'File A New Award',
            'description' => 'Add an award to a user\'s personnel file. Awards can be viewed from within the user\'s personnel file.',
            'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',
        ];
    }

    return $routes;
})->middleware([Authenticate::class, Authorize::class]);
