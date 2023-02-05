<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class PrivacyPolicyController extends Controller
{
    /**
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('PrivacyPolicy');
    }
}
