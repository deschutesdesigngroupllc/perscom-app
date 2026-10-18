<?php

declare(strict_types=1);

namespace App\Http\Controllers\Oidc;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Passport\OidcClaims;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserInfoController extends Controller
{
    public function index(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::guard('passport')->user();

        $scopes = collect(['email', 'profile'])
            ->filter(fn (string $scope): bool => $user->tokenCan($scope))
            ->values()
            ->all();

        return response()->json([
            ...OidcClaims::forScopes($user, $scopes),
            'id' => $user->getKey(),
        ]);
    }
}
