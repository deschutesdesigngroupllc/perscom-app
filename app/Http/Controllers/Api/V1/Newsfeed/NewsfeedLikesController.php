<?php

namespace App\Http\Controllers\Api\V1\Newsfeed;

use App\Models\NewsfeedItem;
use Orion\Http\Controllers\RelationController;

class NewsfeedLikesController extends RelationController
{
    /**
     * @var string
     */
    protected $model = NewsfeedItem::class;

    /**
     * @var string
     */
    protected $relation = 'likes';
}
