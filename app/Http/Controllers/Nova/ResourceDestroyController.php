<?php

namespace App\Http\Controllers\Nova;

use App\Models\Passport\Client;
use App\Models\Passport\Token;
use Laravel\Nova\Http\Requests\DeleteResourceRequest;

class ResourceDestroyController extends \Laravel\Nova\Http\Controllers\ResourceDestroyController
{
    /**
     * Destroy the given resource(s).
     *
     * @param  \Laravel\Nova\Http\Requests\DeleteResourceRequest  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function __invoke(DeleteResourceRequest $request)
    {
        if (\in_array(\get_class($request->findModel()), [Token::class, Client::class], true)) {
            config()->set('nova.actions.resource', null);
        }

        return parent::__invoke($request);
    }
}
