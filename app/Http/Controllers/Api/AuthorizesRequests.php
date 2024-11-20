<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Services\ApiPermissionService;

trait AuthorizesRequests
{
    public function authorize(string $ability, $arguments = []): bool
    {
        $authorized = ApiPermissionService::authorize($ability, $arguments);

        abort_unless($authorized, 403);

        return $authorized;
    }
}
