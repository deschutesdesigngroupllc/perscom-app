<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use SodiumException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Discord signs every interaction with the application's Ed25519 key and requires endpoints to
 * reject anything that does not verify.
 */
class VerifyDiscordSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        $signature = (string) $request->header('X-Signature-Ed25519');
        $timestamp = (string) $request->header('X-Signature-Timestamp');
        $publicKey = (string) config('services.discord.public_key');

        abort_if(blank($signature) || blank($timestamp) || blank($publicKey), 401, 'Invalid request signature.');

        try {
            $verified = ctype_xdigit($signature)
                && ctype_xdigit($publicKey)
                && sodium_crypto_sign_verify_detached(
                    (string) hex2bin($signature),
                    $timestamp.$request->getContent(),
                    (string) hex2bin($publicKey),
                );
        } catch (SodiumException) {
            $verified = false;
        }

        abort_unless($verified, 401, 'Invalid request signature.');

        return $next($request);
    }
}
