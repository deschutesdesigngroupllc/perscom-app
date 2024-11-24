<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Enums\PassportClientType;
use App\Models\PassportToken;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Passport\Guards\TokenGuard;
use PHPOpenSourceSaver\JWTAuth\JWT;

class ApiPermissionService
{
    /**
     * @param  array<string|Model>|string|Model  $arguments
     */
    public static function authorize(string $ability, array|string|Model $arguments = []): bool
    {
        $scope = static::formScope(
            ability: static::mapAbilities($ability),
            model: static::transformResourceName($arguments)
        );

        /** @var TokenGuard $passport */
        $passport = Auth::guard('passport');
        if ($passport->client() && $passport->client()->getAttribute('type') === PassportClientType::CLIENT_CREDENTIALS) {
            /** @var ?PassportToken $token */
            $token = request()->attributes->get('client_credentials_token');

            if (filled($token) && $token->can($scope)) {
                return true;
            }
        }

        if (! Auth::guard('api')->check()) {
            return false;
        }

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

        return in_array($scope, $scopes);
    }

    /**
     * @return string[]
     */
    public static function scopes(): array
    {
        return array_merge(
            collect(config('api.scopes'))->toArray(),
            [
                'profile' => 'Can view your profile',
                'email' => 'Can view your email',
                'openid' => 'Can perform Single Sign On',
            ]
        );
    }

    protected static function mapAbilities(string $ability): string
    {
        return match ($ability) {
            'viewAny' => 'view',
            'forceDelete', 'restore' => 'delete',
            default => $ability
        };
    }

    /**
     * @param  array<string|Model>|string|Model  $model
     */
    protected static function transformResourceName(array|string|Model $model): string
    {
        if (blank($model)) {
            return '';
        }

        $model = Arr::wrap($model)[0];

        return Str::lower(class_basename($model));
    }

    protected static function formScope(string $ability, string $model): string
    {
        if (blank($model)) {
            return $ability;
        }

        return "$ability:$model";
    }
}
