<?php

namespace App\Http\Controllers\Login;

use App\Http\Controllers\Controller;
use App\Models\LoginToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SingleSignOnController extends Controller
{
    protected static int $loginTokenTtl = 60;

    public function index(LoginToken $token): RedirectResponse
    {
        if ($token->created_at->diffInSeconds(Carbon::now()) > self::$loginTokenTtl) {
            abort(403, 'The login token has expired.');
        }

        Auth::guard('web')->loginUsingId($token->user_id);

        $token->delete();

        return redirect()->to(route('nova.pages.home'));
    }

    public function store(): JsonResponse
    {
        if (! $user = Auth::guard('jwt')->user()) {
            abort(403, 'The user in the authentication token is not valid.');
        }

        return response()->json([
            'redirect_to' => route('sso.index', [
                'token' => LoginToken::create([
                    'user_id' => $user->getAuthIdentifier(),
                ]),
            ]),
        ]);
    }
}
