<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\Enableable;
use App\Models\Scopes\EnabledScope;
use App\Traits\CanBeEnabled;
use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * App\Models\Message
 *
 * @property int $id
 * @property string $title
 * @property string $message
 * @property string|null $link_text
 * @property string|null $url
 * @property bool $enabled
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @method static Builder|Message disabled()
 * @method static Builder|Message enabled()
 * @method static Builder|Message newModelQuery()
 * @method static Builder|Message newQuery()
 * @method static Builder|Message onlyTrashed()
 * @method static Builder|Message ordered(string $direction = 'asc')
 * @method static Builder|Message query()
 * @method static Builder|Message whereCreatedAt($value)
 * @method static Builder|Message whereDeletedAt($value)
 * @method static Builder|Message whereEnabled($value)
 * @method static Builder|Message whereId($value)
 * @method static Builder|Message whereLinkText($value)
 * @method static Builder|Message whereMessage($value)
 * @method static Builder|Message whereOrder($value)
 * @method static Builder|Message whereTitle($value)
 * @method static Builder|Message whereUpdatedAt($value)
 * @method static Builder|Message whereUrl($value)
 * @method static Builder|Message withTrashed()
 * @method static Builder|Message withoutTrashed()
 *
 * @mixin \Eloquent
 */
#[ScopedBy(EnabledScope::class)]
class Message extends Model implements Enableable, Sortable
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
}
