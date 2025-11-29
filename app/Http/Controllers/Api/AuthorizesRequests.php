<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Services\ApiPermissionService;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

trait AuthorizesRequests
{
    public function authorize(string $ability, array|string|Model $arguments = []): Response
    {
        $authorized = ApiPermissionService::authorize($ability, $arguments);

        abort_unless($authorized, 403);

        return Response::allow();
    }
}
