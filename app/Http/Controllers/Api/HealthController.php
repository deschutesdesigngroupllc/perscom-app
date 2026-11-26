<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JsonException;
use Spatie\Health\ResultStores\ResultStore;
use Symfony\Component\HttpFoundation\JsonResponse;

use function response;

class HealthController extends Controller
{
    /**
     * Return the latest health check results.
     *
     * @throws JsonException when the stored check results are not valid JSON
     */
    public function __invoke(Request $request, ResultStore $resultStore): JsonResponse
    {
        $checkResults = $resultStore->latestResults();

        if (blank($checkResults)) {
            return response()->json(status: 204);
        }

        return response()->json([
            'data' => json_decode($checkResults->toJson(), true, 512, JSON_THROW_ON_ERROR),
        ]);
    }
}
