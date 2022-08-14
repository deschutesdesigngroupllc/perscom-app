<?php

namespace App\Models;

use Codinglabs\FeatureFlags\Models\Feature as BaseFeatureModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Nova\Actions\Actionable;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class Feature extends BaseFeatureModel
{
    use CentralConnection;
    use HasFactory;
    use Actionable;

    /**
     * @var string[]
     */
    protected $attributes = [
        'state' => 'off',
    ];
}
