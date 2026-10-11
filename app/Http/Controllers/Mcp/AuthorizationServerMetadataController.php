<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mcp;

use App\Http\Controllers\Controller;
use App\Mcp\ApiResourceRegistry;
use Illuminate\Http\JsonResponse;

/**
 * OAuth 2.0 Authorization Server Metadata (RFC 8414) pointing MCP clients at the tenant's
 * Passport endpoints, including dynamic client registration.
 */
class AuthorizationServerMetadataController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'issuer' => request()->getSchemeAndHttpHost(),
            'authorization_endpoint' => route('passport.authorizations.authorize'),
            'token_endpoint' => route('passport.token'),
            'registration_endpoint' => route('mcp.oauth.register'),
            'jwks_uri' => route('oidc.jwks'),
            'response_types_supported' => ['code'],
            'grant_types_supported' => ['authorization_code', 'refresh_token'],
            'code_challenge_methods_supported' => ['S256'],
            'token_endpoint_auth_methods_supported' => ['none', 'client_secret_post', 'client_secret_basic'],
            'scopes_supported' => ApiResourceRegistry::scopes(),
        ]);
    }
}
