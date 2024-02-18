<?php

declare(strict_types=1);

return [
    'passport' => [

        /**
         * Place your Passport and OpenID Connect scopes here.
         * To receive an `id_token, you should at least provide the openid scope.
         */
        'tokens_can' => [
            'openid' => 'Can perform single-sign on',
            'email' => 'Can access the authenticated user\'s email',
            'profile' => 'Can access the authenticated user\'s profile',
            'tenant' => 'Can access the authenticated user\'s organization profile',
        ],
    ],

    /**
     * Place your custom claim sets here.
     */
    'custom_claim_sets' => [
        'tenant' => [
            'tenant_name',
            'tenant_sub',
        ],
    ],

    /**
     * You can override the repositories below.
     */
    'repositories' => [
        'identity' => \App\Support\OpenIDConnect\Repositories\IdentityRepository::class,
        'scope' => \Laravel\Passport\Bridge\ScopeRepository::class,
    ],

    /**
     * The signer to be used
     * Can be Ecdsa, Hmac or RSA
     */
    'signer' => \Lcobucci\JWT\Signer\Hmac\Sha256::class,
];
