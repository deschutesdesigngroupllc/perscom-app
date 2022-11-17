<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Whitecube\NovaPage\Pages\Manager;

class PageController extends Controller
{
    public function show(Request $request, Manager $page)
    {
    	$page->loadForRoute($request->route());

    	return Inertia::render();
    }
}
