<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Home', [
            'banners' => Banner::all(),
        ]);
    }
}
