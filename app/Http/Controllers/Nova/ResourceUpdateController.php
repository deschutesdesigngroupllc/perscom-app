<?php

namespace App\Http\Controllers\Nova;

use App\Models\PassportClient;
use App\Models\PassportToken;
use Laravel\Nova\Http\Requests\UpdateResourceRequest;

class ResourceUpdateController extends \Laravel\Nova\Http\Controllers\ResourceUpdateController
{
    /**
     * Create a new resource.
     *
     * @param  \Laravel\Nova\Http\Requests\UpdateResourceRequest  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    public function __invoke(UpdateResourceRequest $request)
    {
        if (\in_array(\get_class($request->findModel()), [PassportToken::class, PassportClient::class], true)) {
            config()->set('nova.actions.resource', null);
        }

        return parent::__invoke($request);
    }
}
