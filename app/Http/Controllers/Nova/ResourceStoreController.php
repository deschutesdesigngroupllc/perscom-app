<?php

namespace App\Http\Controllers\Nova;

use App\Models\Passport\Client;
use App\Models\Passport\Token;
use Laravel\Nova\Http\Requests\CreateResourceRequest;

class ResourceStoreController extends \Laravel\Nova\Http\Controllers\ResourceStoreController
{
    /**
     * Create a new resource.
     *
     * @param  \Laravel\Nova\Http\Requests\CreateResourceRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(CreateResourceRequest $request)
    {
        if (\in_array(\get_class($request->findModel()), [Token::class, Client::class], true)) {
            config()->set('nova.actions.resource', null);
        }

        return parent::__invoke($request);
    }
}
