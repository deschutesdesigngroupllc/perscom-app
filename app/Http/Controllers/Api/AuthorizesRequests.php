<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Services\ApiPermissionService;

trait AuthorizesRequests
{
    public function authorize(string $ability, $arguments = []): bool
    {
        return ApiPermissionService::authorize($ability, $arguments);
    }
}
