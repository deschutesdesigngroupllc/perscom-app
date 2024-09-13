<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Health\ResultStores\ResultStore;
use Symfony\Component\HttpFoundation\JsonResponse;

use function response;

class HealthController extends Controller
{
    public function __invoke(Request $request, ResultStore $resultStore): JsonResponse
    {
        $checkResults = $resultStore->latestResults();

        return response()->json([
            'data' => json_decode($checkResults->toJson(), true),
        ]);
    }
}
