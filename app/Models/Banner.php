<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * @property int $id
 * @property string|null $title
 * @property string $message
 * @property string|null $link_url
 * @property string|null $link_text
 * @property string|null $background_color
 * @property string|null $text_color
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Banner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Banner newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Banner onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Banner query()
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereBackgroundColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereLinkText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereLinkUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereTextColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Banner withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Banner extends Model
{
    use CentralConnection;
    use ClearsResponseCache;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'message',
        'link_url',
        'link_text',
        'background_color',
        'text_color',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
