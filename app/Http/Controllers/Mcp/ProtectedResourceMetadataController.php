<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mcp;

use App\Http\Controllers\Controller;
use App\Mcp\ApiResourceRegistry;
use App\Models\Tenant;
use App\Settings\OrganizationSettings;
use Illuminate\Http\JsonResponse;

class ProtectedResourceMetadataController extends Controller
{
    public function __invoke(): JsonResponse
    {
        /** @var ?Tenant $tenant */
        $tenant = tenant();

        /** @var OrganizationSettings $settings */
        $settings = resolve(OrganizationSettings::class);

        return response()->json([
            'resource' => route('mcp.server'),
            'authorization_servers' => [request()->getSchemeAndHttpHost()],
            'scopes_supported' => ApiResourceRegistry::scopes(),
            'bearer_methods_supported' => ['header'],
            'resource_name' => sprintf('%s PERSCOM', $tenant?->name ?? $settings->name),
        ]);
    }
}
