<?php

namespace App\Http\Controllers\Oidc;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserInfoController extends Controller
{
    public function index(): JsonResponse
    {
        $user = Auth::guard('passport')->user();

        $response = collect([
            'sub' => $user?->getAuthIdentifier(),
        ]);

        if ($user->tokenCan('email')) {
            $response = $response->merge([
                'email' => $user->email,
            ]);
        }

        if ($user->tokenCan('profile')) {
            $profile = collect($user)->only([ // @phpstan-ignore-line
                'name',
                'profile_photo_url',
                'cover_photo_url',
            ]);

            $response = $response->merge($profile);
        }

        return response()->json($response->unique());
    }
}
