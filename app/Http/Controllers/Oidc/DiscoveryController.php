<?php

namespace App\Http\Controllers\Oidc;

use App\Http\Controllers\Controller;

class DiscoveryController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json([
            'issuer' => tenant()->url,
            'authorization_endpoint' => tenant()->route('passport.authorizations.authorize'),
            'token_endpoint' => tenant()->route('passport.token'),
            'userinfo_endpoint' => tenant()->route('oidc.userinfo'),
            'grant_types_supported' => [
                'authorization_code',
                'implicit',
                'refresh_token',
            ],
            'subject_types_supported' => [
                'public',
            ],
            'id_token_signing_alg_values_supported' => [
                'RS256',
            ],
            'scopes_supported' => [
                'openid',
                'profile',
                'email',
            ],
            'token_endpoint_auth_methods_supported' => [
                'client_secret_basic',
                'client_secret_post',
            ],
            'claims_supported' => [
                'jti',
                'iat',
                'nbf',
                'exp',
                'sub',
                'scopes',
            ],
            'end_session_endpoint' => tenant()->route('oidc.logout'),
        ]);
    }
}
