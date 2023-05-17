<?php

namespace App\Http\Controllers\Oidc;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function index(Request $request)
    {
        optional(Auth::guard('passport')->user() ?? Auth::guard('web')->user(), static function (User $user) {
            Auth::guard('web')->logout();

            $user->token()?->revoke();
        });

        $redirect = $request->get('post_logout_redirect_uri') ?? tenant()->url;

        if ($request->expectsJson()) {
            return response()->json([
                'redirect' => $redirect,
            ]);
        }

        return redirect($redirect);
    }
}
