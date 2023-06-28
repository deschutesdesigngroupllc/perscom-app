<?php

namespace App\Http\Controllers\Api\V1\Newsfeed;

use App\Http\Resources\NewsfeedResource;
use App\Models\Newsfeed;
use Orion\Http\Controllers\Controller;

class NewsfeedController extends Controller
{
    /**
     * @var string
     */
    protected $model = Newsfeed::class;

    /**
     * @var string
     */
    protected $resource = NewsfeedResource::class;

    /**
     * The relations that are loaded by default together with a resource.
     */
    public function alwaysIncludes(): array
    {
        return ['causer', 'subject', 'subject?.user'];
    }
}
