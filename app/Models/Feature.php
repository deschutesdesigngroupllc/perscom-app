<?php

namespace App\Models;

use Codinglabs\FeatureFlags\Enums\FeatureState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Codinglabs\FeatureFlags\Models\Feature as BaseFeatureModel;
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
