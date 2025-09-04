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
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
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
 * @property Collection<int, AlertChannel>|null $channels
 * @property bool $enabled
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder<static>|Alert disabled()
 * @method static Builder<static>|Alert enabled()
 * @method static Builder<static>|Alert newModelQuery()
 * @method static Builder<static>|Alert newQuery()
 * @method static Builder<static>|Alert ordered(string $direction = 'asc')
 * @method static Builder<static>|Alert query()
 * @method static Builder<static>|Alert whereChannels($value)
 * @method static Builder<static>|Alert whereCreatedAt($value)
 * @method static Builder<static>|Alert whereEnabled($value)
 * @method static Builder<static>|Alert whereId($value)
 * @method static Builder<static>|Alert whereLinkText($value)
 * @method static Builder<static>|Alert whereMessage($value)
 * @method static Builder<static>|Alert whereOrder($value)
 * @method static Builder<static>|Alert whereTitle($value)
 * @method static Builder<static>|Alert whereUpdatedAt($value)
 * @method static Builder<static>|Alert whereUrl($value)
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
    use SortableTrait;

    protected $attributes = [
        'enabled' => true,
    ];

    protected $fillable = [
        'title',
        'message',
        'order',
        'url',
        'link_text',
        'created_at',
        'updated_at',
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
