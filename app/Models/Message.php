<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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
 * @property bool $active
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static Builder|Message active()
 * @method static Builder|Message newModelQuery()
 * @method static Builder|Message newQuery()
 * @method static Builder|Message ordered(string $direction = 'asc')
 * @method static Builder|Message query()
 * @method static Builder|Message whereActive($value)
 * @method static Builder|Message whereCreatedAt($value)
 * @method static Builder|Message whereId($value)
 * @method static Builder|Message whereLinkText($value)
 * @method static Builder|Message whereMessage($value)
 * @method static Builder|Message whereOrder($value)
 * @method static Builder|Message whereTitle($value)
 * @method static Builder|Message whereUpdatedAt($value)
 * @method static Builder|Message whereUrl($value)
 *
 * @mixin \Eloquent
 */
class Message extends Model implements Sortable
{
    use CentralConnection;
    use SortableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['title', 'message', 'active', 'order', 'url', 'link_text'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * @return mixed
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('active', '=', true);
    }
}
