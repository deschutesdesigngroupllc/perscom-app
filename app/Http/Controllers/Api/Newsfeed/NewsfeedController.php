<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Newsfeed;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Resources\NewsfeedResource;
use App\Models\Newsfeed;
use Orion\Http\Controllers\Controller;

class NewsfeedController extends Controller
{
    use AuthorizesRequests;

    protected $model = Newsfeed::class;

    protected $resource = NewsfeedResource::class;
}
