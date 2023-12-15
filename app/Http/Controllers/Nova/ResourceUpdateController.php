<?php

namespace App\Http\Controllers\Nova;

use App\Models\PassportClient;
use App\Models\PassportToken;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Laravel\Nova\Http\Requests\UpdateResourceRequest;

class ResourceUpdateController extends \Laravel\Nova\Http\Controllers\ResourceUpdateController
{
    /**
     * @throws HttpResponseException
     */
    public function __invoke(UpdateResourceRequest $request): JsonResponse
    {
        if (\in_array(\get_class($request->findModel()), [PassportToken::class, PassportClient::class], true)) {
            config()->set('nova.actions.resource', null);
        }

        return parent::__invoke($request);
    }
}
