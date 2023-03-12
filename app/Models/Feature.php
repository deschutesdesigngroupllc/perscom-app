<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Actionable;
use Stancl\Tenancy\Database\Concerns\CentralConnection;
use Symfony\Component\Finder\Finder;

/**
 * App\Models\Feature
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
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
class Feature extends Model
{
    use CentralConnection;
    use HasFactory;
    use Actionable;

    /**
     * @var string[]
     */
    protected $casts = [
        'scope' => 'string',
    ];

    /**
     * @param  Builder  $query
     * @param  Tenant  $tenant
     * @return void
     */
    public function scopeForTenant(Builder $query, Tenant $tenant)
    {
        $query->where('scope', \Laravel\Pennant\Feature::serializeScope($tenant));
    }

    /**
     * @return mixed
     */
    public static function options()
    {
        return Collection::make(
            (new Finder())->files()->name('*.php')->depth(0)->in(base_path('app/Features'))
        )->mapWithKeys(function ($file) {
            $class = "App\\Features\\{$file->getBasename('.php')}";

            return [$class => $class];
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'scope');
    }
}
