<?php

namespace App\Http\Controllers\Passport;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\TokenRepository;
use Psr\Http\Message\ServerRequestInterface;

class AuthorizationController extends \Laravel\Passport\Http\Controllers\AuthorizationController
{
    /**
     * Authorize a client to access the user's account.
     *
     * @param  \Psr\Http\Message\ServerRequestInterface  $psrRequest
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Passport\ClientRepository  $clients
     * @param  \Laravel\Passport\TokenRepository  $tokens
     * @return \Illuminate\Http\Response
     */
    public function authorize(ServerRequestInterface $psrRequest, Request $request, ClientRepository $clients, TokenRepository $tokens)
    {
        $authRequest = $this->withErrorHandling(function () use ($psrRequest) {
            return $this->server->validateAuthorizationRequest($psrRequest);
        });

        $scopes = $this->parseScopes($authRequest);

        $token = $tokens->findValidToken($user = $request->user(), $client = $clients->find($authRequest->getClient()
                                                                                                        ->getIdentifier()));

        if (($token && $token->scopes === collect($scopes)->pluck('id')->all()) || $client->skipsAuthorization()) {
            return $this->approveRequest($authRequest, $user);
        }

        $request->session()->put('authToken', $authToken = Str::random());
        $request->session()->put('authRequest', $authRequest);

        return Inertia::render('Passport/Authorize', [
            'client' => $client->id,
            'name' => $client->name,
            'scopes' => $scopes,
            'state' => $request->state,
            'authToken' => $authToken,
            'csrfToken' => csrf_token(),
        ])->toResponse($request);
    }
}
