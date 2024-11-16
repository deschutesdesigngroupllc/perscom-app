<?php

declare(strict_types=1);

namespace App\Http\Controllers\Oidc;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class UserInfoController extends Controller
{
    public function index(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::guard('passport')->user();

        $response = collect($claims = $user->getJWTCustomClaims());
        $response->put('id', $user->getKey());

        if (! $user->tokenCan('email')) {
            $response->forget(['email', 'email_verified']);
        }

        if (! $user->tokenCan('profile')) {
            $response->forget(array_keys(Arr::except($claims, ['id', 'email', 'email_verified'])));
        }

        return response()->json($response->toArray());
    }
}
