<?php

namespace App\Http\Controllers\Api\V1\Newsfeed;

use App\Http\Resources\NewsfeedResource;
use App\Models\NewsfeedItem;
use Orion\Http\Controllers\Controller;

class NewsfeedController extends Controller
{
    /**
     * @var string
     */
    protected $model = NewsfeedItem::class;

    /**
     * @var string
     */
    protected $resource = NewsfeedResource::class;
}
