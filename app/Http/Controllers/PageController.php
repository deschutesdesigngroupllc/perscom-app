<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PageController extends Controller
{
    public function show(Request $request, Page $page)
    {
        return Inertia::render('Pages/Page', [
            'content' => $page->content,
        ]);
    }
}
