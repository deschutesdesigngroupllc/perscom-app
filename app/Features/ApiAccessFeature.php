<?php

namespace App\Features;

use App\Services\JwtService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Spark\Plan;

class ApiAccessFeature extends BaseFeature
{
    public function resolve(?string $scope): bool
    {
        $tenant = $this->resolveTenant($scope);

        return match (true) {
            Request::isCentralRequest() => false,
            Request::isDemoMode() => true,
            Auth::guard('jwt')->check() && JwtService::signedByPerscom(JWTAuth::getToken()) => true, // @phpstan-ignore-line
            $tenant->onTrial() => true,
            optional($tenant->sparkPlan(), static function (Plan $plan) {
                return \in_array(__CLASS__, $plan->options, true);
            }) => true,
            default => false,
        };
    }
}
