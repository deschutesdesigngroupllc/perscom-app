<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Newsfeed;

use App\Models\Newsfeed;
use Orion\Http\Controllers\RelationController;

class NewsfeedLikesController extends RelationController
{
    protected $model = Newsfeed::class;

    protected $relation = 'likes';

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
