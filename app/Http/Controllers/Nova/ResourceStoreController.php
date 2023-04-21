<?php

namespace App\Http\Controllers\Nova;

use App\Models\PassportClient;
use App\Models\PassportToken;
use Laravel\Nova\Http\Requests\CreateResourceRequest;

class ResourceStoreController extends \Laravel\Nova\Http\Controllers\ResourceStoreController
{
    /**
     * Create a new resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(CreateResourceRequest $request)
    {
        if (\in_array(\get_class($request->findModel()), [PassportToken::class, PassportClient::class], true)) {
            config()->set('nova.actions.resource', null);
        }

        return parent::__invoke($request);
    }
}
