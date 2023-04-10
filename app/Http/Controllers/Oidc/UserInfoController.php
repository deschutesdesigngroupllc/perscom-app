<?php

namespace App\Http\Controllers\Oidc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserInfoController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = Auth::guard('passport')->user();

        $response = collect([
            'sub' => $user?->getAuthIdentifier(),
        ]);

        if ($user?->tokenCan('email')) {
            $response = $response->merge([
                'email' => $user?->email,
            ]);
        }

        if ($user?->tokenCan('profile')) {
            $profile = collect($user)->only([
                'name',
                'profile_photo',
                'cover_photo',
            ]);

            $response = $response->merge($profile);
        }

        return response()->json($response->unique());
    }
}
