<?php

namespace App\Http\Controllers\Api\V1\Units;

use App\Models\Unit;
use Orion\Http\Controllers\RelationController;

class UnitsUsersController extends RelationController
{
    /**
     * @var string
     */
    protected $model = Unit::class;

    /**
     * @var string
     */
    protected $relation = 'users';

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
