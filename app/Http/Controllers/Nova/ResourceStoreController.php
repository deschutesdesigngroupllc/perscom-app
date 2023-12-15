<?php

namespace App\Http\Controllers\Nova;

use App\Models\EventRegistration;
use App\Models\PassportClient;
use App\Models\PassportToken;
use Illuminate\Http\JsonResponse;
use Laravel\Nova\Http\Requests\CreateResourceRequest;

class ResourceStoreController extends \Laravel\Nova\Http\Controllers\ResourceStoreController
{
    public function __invoke(CreateResourceRequest $request): JsonResponse
    {
        if (\in_array(\get_class($request->findModel()), [PassportToken::class, PassportClient::class, EventRegistration::class], true)) {
            config()->set('nova.actions.resource', null);
        }

        return parent::__invoke($request);
    }
}
