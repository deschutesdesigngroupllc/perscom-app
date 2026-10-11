<?php

declare(strict_types=1);

namespace App\Http\Controllers\Oidc;

use App\Http\Controllers\Controller;
use App\Support\Passport\JsonWebKey;
use Illuminate\Http\JsonResponse;

class JwksController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'keys' => [
                JsonWebKey::publicKey(),
            ],
        ]);
    }
}
