<?php

namespace App\Http\Controllers\Swagger;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('swagger/Home', [
            'url' => config('app.api_url').'/'.config('app.api_version').'/spec.yaml',
        ]);
    }
}
