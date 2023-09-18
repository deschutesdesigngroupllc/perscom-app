<?php

namespace App\Http\Controllers\Oidc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function index(Request $request)
    {
        Auth::logout();

        $redirect = $request->input('post_logout_redirect_uri');

        $tokenHint = $request->input('id_token_hint');

        if ($tokenHint) {

        }

        if ($redirect) {

        }

        if ($request->expectsJson()) {
            return response()->json([
                'redirect' => $redirect,
            ]);
        }

        return redirect($redirect);
    }
}
