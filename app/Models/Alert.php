<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\Enableable;
use App\Models\Enums\AlertChannel;
use App\Models\Scopes\EnabledScope;
use App\Observers\AlertObserver;
use App\Traits\CanBeEnabled;
use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * App\Models\Alert
 *
 * @property int $id
 * @property string $title
 * @property string $message
 * @property string|null $link_text
 * @property string|null $url
 * @property AsEnumCollection|null $channels
 * @property bool $enabled
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @method static Builder|Alert disabled()
 * @method static Builder|Alert enabled()
 * @method static Builder|Alert newModelQuery()
 * @method static Builder|Alert newQuery()
 * @method static Builder|Alert onlyTrashed()
 * @method static Builder|Alert ordered(string $direction = 'asc')
 * @method static Builder|Alert query()
 * @method static Builder|Alert whereChannels($value)
 * @method static Builder|Alert whereCreatedAt($value)
 * @method static Builder|Alert whereDeletedAt($value)
 * @method static Builder|Alert whereEnabled($value)
 * @method static Builder|Alert whereId($value)
 * @method static Builder|Alert whereLinkText($value)
 * @method static Builder|Alert whereMessage($value)
 * @method static Builder|Alert whereOrder($value)
 * @method static Builder|Alert whereTitle($value)
 * @method static Builder|Alert whereUpdatedAt($value)
 * @method static Builder|Alert whereUrl($value)
 * @method static Builder|Alert withTrashed()
 * @method static Builder|Alert withoutTrashed()
 *
 * @mixin \Eloquent
 */
#[ObservedBy(AlertObserver::class)]
#[ScopedBy(EnabledScope::class)]
class Alert extends Model implements Enableable, Sortable
{
    use CanBeEnabled;
    use CentralConnection;
    use ClearsResponseCache;
    use SoftDeletes;
    use SortableTrait;

    protected $fillable = [
        'title',
        'message',
        'order',
        'url',
        'link_text',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'channels' => AsEnumCollection::of(AlertChannel::class),
        ];
    }
}
