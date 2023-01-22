<?php

namespace App\Http\Controllers\Swagger;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Swagger/Home', [
            'url' => config('app.api_url').'/'.config('app.api_version').'/spec.yaml',
        ]);
    }
}
