<?php

namespace App\Models;

class Activity extends \Spatie\Activitylog\Models\Activity
{
    /**
     * @var string[]
     */
    protected $casts = [
        'properties' => 'collection',
        'description' => 'array',
    ];
}
