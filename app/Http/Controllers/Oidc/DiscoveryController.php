<?php

declare(strict_types=1);

namespace App\Http\Controllers\Oidc;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;

class DiscoveryController extends Controller
{
    public function __construct(protected ?Tenant $tenant = null)
    {
        $this->tenant = tenant();
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'issuer' => $this->tenant
                ? $this->tenant->url
                : config('app.url'),
            'authorization_endpoint' => $this->tenant
                ? $this->tenant->route('passport.authorizations.authorize')
                : route('passport.authorizations.authorize'),
            'token_endpoint' => $this->tenant
                ? $this->tenant->route('passport.token')
                : route('passport.token'),
            'userinfo_endpoint' => $this->tenant
                ? $this->tenant->route('oidc.userinfo')
                : route('oidc.userinfo'),
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
                'fragment',
            ],
            'id_token_signing_alg_values_supported' => [
                'HS256',
            ],
            'scopes_supported' => [
                'openid',
                'profile',
                'email',
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
                'phone_number',
                'tenant',
                'roles',
                'locale',
                'zoneinfo',
                'updated_at',
            ],
            'end_session_endpoint' => $this->tenant
                ? $this->tenant->route('oidc.logout')
                : route('oidc.logout'),
        ]);
    }
}
