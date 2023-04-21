<?php

namespace App\Http\Controllers\Nova;

use App\Models\PassportClient;
use App\Models\PassportToken;
use Laravel\Nova\Http\Requests\DeleteResourceRequest;

class ResourceDestroyController extends \Laravel\Nova\Http\Controllers\ResourceDestroyController
{
    /**
     * Destroy the given resource(s).
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function __invoke(DeleteResourceRequest $request)
    {
        if (\in_array(\get_class($request->findModel()), [PassportToken::class, PassportClient::class], true)) {
            config()->set('nova.actions.resource', null);
        }

        return parent::__invoke($request);
    }
}
