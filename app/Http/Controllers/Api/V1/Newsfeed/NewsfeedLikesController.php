<?php

namespace App\Http\Controllers\Api\V1\Newsfeed;

use App\Models\Newsfeed;
use Orion\Http\Controllers\RelationController;

class NewsfeedLikesController extends RelationController
{
    /**
     * @var string
     */
    protected $model = Newsfeed::class;

    /**
     * @var string
     */
    protected $relation = 'likes';

    /**
     * @return string[]
     */
    public function includes(): array
    {
        return [
            'assignment_records',
            'award_records',
            'combat_records',
            'position',
            'qualification_records',
            'rank',
            'rank_records',
            'service_records',
            'specialty',
            'status',
            'unit',
        ];
    }
}
