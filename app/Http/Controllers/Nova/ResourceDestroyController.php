<?php

namespace App\Http\Controllers\Nova;

use App\Models\PassportClient;
use App\Models\PassportToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Laravel\Nova\Http\Requests\DeleteResourceRequest;

class ResourceDestroyController extends \Laravel\Nova\Http\Controllers\ResourceDestroyController
{
    public function __invoke(DeleteResourceRequest $request): Response|JsonResponse
    {
        if (\in_array(\get_class($request->findModel()), [PassportToken::class, PassportClient::class], true)) {
            config()->set('nova.actions.resource', null);
        }

        return parent::__invoke($request);
    }
}
