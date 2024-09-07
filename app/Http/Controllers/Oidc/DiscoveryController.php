<?php

declare(strict_types=1);

namespace App\Http\Controllers\Oidc;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;

class DiscoveryController extends Controller
{
    public function index(Tenant $tenant): JsonResponse
    {
        return response()->json([
            'issuer' => $tenant->url,
            'authorization_endpoint' => $tenant->route('passport.authorizations.authorize'),
            'token_endpoint' => $tenant->route('passport.token'),
            'userinfo_endpoint' => $tenant->route('oidc.userinfo'),
            'grant_types_supported' => [
                'authorization_code',
                'implicit',
                'refresh_token',
                'client_credentials',
                'password',
            ],
            'subject_types_supported' => [
                'public',
            ],
            'response_types_supported' => [
                'code',
                'token',
            ],
            'response_modes_supported' => [
                'query',
            ],
            'id_token_signing_alg_values_supported' => [
                'HS256',
            ],
            'token_endpoint_auth_signing_alg_values_supported' => [
                'RS256',
            ],
            'scopes_supported' => [
                'openid',
                'profile',
                'email',
                'tenant',
            ],
            'token_endpoint_auth_methods_supported' => [
                'client_secret_basic',
                'client_secret_post',
            ],
            'claims_supported' => [
                'aud',
                'iss',
                'iat',
                'exp',
                'sub',
                'name',
                'preferred_username',
                'profile',
                'email',
                'email_verified',
                'picture',
                'tenant',
                'locale',
                'zonefinfo',
                'updated_at',
            ],
            'end_session_endpoint' => $tenant->route('oidc.logout'),
        ]);
    }
}
