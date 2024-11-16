<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\JWT;

class ApiPermissionService
{
    public static function authorize(string $ability, $arguments = []): bool
    {
        if (! Auth::guard('api')->check()) {
            return false;
        }

        $scope = static::formScope(
            ability: static::mapAbilities($ability),
            model: static::transformResourceName($arguments)
        );

        /** @var User $user */
        $user = Auth::guard('passport')->user();
        if (Auth::guard('passport')->check() && $user->tokenCan($scope)) {
            return true;
        }

        /** @var JWT $jwt */
        $jwt = Auth::guard('jwt');
        $scopes = rescue(fn () => Arr::wrap($jwt->getPayload()->get('scopes')), [], false);

        if (in_array('*', $scopes)) {
            return true;
        }

        abort_unless(in_array($scope, $scopes), 403, 'The API key provided does not have the correct scopes to perform the requested action.');

        return true;
    }

    public static function scopes(): array
    {
        return array_merge(
            collect(config('api.scopes'))->toArray(),
            [
                'profile' => 'Can view your profile',
                'email' => 'Can view your email',
                'openid' => 'Can log your account in and out',
            ]
        );
    }

    protected static function mapAbilities($ability): string
    {
        return match ($ability) {
            'viewAny' => 'view',
            'forceDelete', 'restore' => 'delete',
            default => $ability
        };
    }

    protected static function transformResourceName($model): string
    {
        $model = Arr::wrap($model)[0];

        return Str::lower(class_basename($model));
    }

    protected static function formScope($ability, $model): string
    {
        return "$ability:$model";
    }
}
