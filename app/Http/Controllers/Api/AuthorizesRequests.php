<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Services\ApiPermissionService;
use Illuminate\Auth\Access\Response;

trait AuthorizesRequests
{
    public function authorize(string $ability, $arguments = []): Response
    {
        $authorized = ApiPermissionService::authorize($ability, $arguments);

        abort_unless($authorized, 403);

        return Response::allow();
    }
}
