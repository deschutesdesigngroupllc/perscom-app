<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Newsfeed;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Models\Newsfeed;
use Orion\Http\Controllers\RelationController;

class NewsfeedLikesController extends RelationController
{
    use AuthorizesRequests;

    protected $model = Newsfeed::class;

    protected $relation = 'likes';

    /**
     * @return array<int, string>
     */
    public function includes(): array
    {
        return [
            'assignment_records',
            'assignment_records.*',
            'award_records',
            'award_records.*',
            'combat_records',
            'combat_records.*',
            'position',
            'qualification_records',
            'rank',
            'rank_records',
            'rank_records.*',
            'service_records',
            'service_records.*',
            'specialty',
            'status',
            'unit',
            'unit.*',
        ];
    }
}
