<?php

namespace App\Models;

use Codinglabs\FeatureFlags\Models\Feature as BaseFeatureModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Nova\Actions\Actionable;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * App\Models\Feature
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property \Codinglabs\FeatureFlags\Enums\FeatureState $state
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Action> $actions
 * @property-read int|null $actions_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Feature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Feature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Feature query()
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
