<?php

declare(strict_types=1);

namespace App\Support\Passport;

use Laravel\Passport\Passport;
use RuntimeException;

class JsonWebKey
{
    /**
     * The public JSON Web Key used to verify tokens signed with the Passport private key.
     *
     * @return array{kty: string, use: string, alg: string, kid: string, n: string, e: string}
     */
    public static function publicKey(): array
    {
        $components = static::components();

        return [
            'kty' => 'RSA',
            'use' => 'sig',
            'alg' => 'RS256',
            'kid' => static::thumbprint(),
            'n' => $components['n'],
            'e' => $components['e'],
        ];
    }

    /**
     * The RFC 7638 thumbprint of the public key, used as the key ID.
     */
    public static function thumbprint(): string
    {
        $components = static::components();

        $canonical = json_encode([
            'e' => $components['e'],
            'kty' => 'RSA',
            'n' => $components['n'],
        ], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);

        return static::base64UrlEncode(hash('sha256', $canonical, true));
    }

    /**
     * @return array{n: string, e: string}
     */
    protected static function components(): array
    {
        $key = openssl_pkey_get_public(static::publicKeyContents());

        if ($key === false) {
            throw new RuntimeException('Unable to load the OAuth public key.');
        }

        $details = openssl_pkey_get_details($key);

        if ($details === false || ! isset($details['rsa']['n'], $details['rsa']['e'])) {
            throw new RuntimeException('The OAuth public key is not a valid RSA key.');
        }

        return [
            'n' => static::base64UrlEncode($details['rsa']['n']),
            'e' => static::base64UrlEncode($details['rsa']['e']),
        ];
    }

    protected static function publicKeyContents(): string
    {
        $key = str_replace('\\n', "\n", (string) config('passport.public_key'));

        if (filled($key)) {
            return $key;
        }

        $contents = @file_get_contents(Passport::keyPath('oauth-public.key'));

        if ($contents === false) {
            throw new RuntimeException('Unable to read the OAuth public key.');
        }

        return $contents;
    }

    protected static function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
