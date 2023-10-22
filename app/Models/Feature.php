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
 * @property string $scope
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Action> $actions
 * @property-read int|null $actions_count
 * @property-read \App\Models\Tenant|null $tenant
 *
 * @method static Builder|Feature newModelQuery()
 * @method static Builder|Feature newQuery()
 * @method static Builder|Feature query()
 * @method static Builder|Feature tenant(\App\Models\Tenant $tenant)
 * @method static Builder|Feature whereCreatedAt($value)
 * @method static Builder|Feature whereId($value)
 * @method static Builder|Feature whereName($value)
 * @method static Builder|Feature whereScope($value)
 * @method static Builder|Feature whereUpdatedAt($value)
 * @method static Builder|Feature whereValue($value)
 *
 * @mixin \Eloquent
 */
class Feature extends Model
{
    use Actionable;
    use CentralConnection;
    use HasFactory;

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'scope' => 'string',
    ];

    /**
     * @return void
     */
    public function scopeTenant(Builder $query, Tenant $tenant)
    {
        $query->where('scope', (string) $tenant->getTenantKey());
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
