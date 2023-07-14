<?php

namespace App\Http\Controllers\Oidc;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserInfoController extends Controller
{
    public function index(Request $request): JsonResponse
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
            /** @var array<string> $keys */
            $keys = [
                'name',
                'profile_photo_url',
                'cover_photo_url',
            ];

            $profile = collect($user)->only($keys);

            $response = $response->merge($profile);
        }

        return response()->json($response->unique());
    }
}
