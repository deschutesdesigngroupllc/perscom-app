<?php

declare(strict_types=1);

namespace App\Http\Controllers\Oidc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Token;
use Symfony\Component\HttpFoundation\Response;

class LogoutController extends Controller
{
    public function index(Request $request): Response
    {
        if (Auth::getDefaultDriver() === 'api' && $user = Auth::guard('api')->user()) {
            $user->tokens->each(fn (Token $token) => $token->revoke());
        } else {
            Auth::logout();
        }

        if ($request->expectsJson()) {
            return response('', Response::HTTP_NO_CONTENT);
        }

        return redirect($request->input('post_logout_redirect_uri') ?? tenant()->url);
    }
}
