<?php

namespace App\Http\Controllers\Oidc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogoutController extends Controller
{
    public function index(Request $request): Response
    {
        Auth::logout();

        $redirect = $request->input('post_logout_redirect_uri');

        return redirect($redirect ?? tenant()->url);
    }
}
