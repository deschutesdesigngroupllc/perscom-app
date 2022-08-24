<?php

namespace App\Http\Controllers\Nova;

use App\Models\Passport\Token;
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
		if ($request->findModel() instanceof Token) {
			config()->set('nova.actions.resource', null);
		}

		return parent::__invoke($request);
	}
}
