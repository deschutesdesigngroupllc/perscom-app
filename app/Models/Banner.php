<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * @property int $id
 * @property string|null $title
 * @property string $message
 * @property string|null $link_url
 * @property string|null $link_text
 * @property string|null $background_color
 * @property string|null $text_color
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder<static>|Banner newModelQuery()
 * @method static Builder<static>|Banner newQuery()
 * @method static Builder<static>|Banner query()
 * @method static Builder<static>|Banner whereBackgroundColor($value)
 * @method static Builder<static>|Banner whereCreatedAt($value)
 * @method static Builder<static>|Banner whereId($value)
 * @method static Builder<static>|Banner whereLinkText($value)
 * @method static Builder<static>|Banner whereLinkUrl($value)
 * @method static Builder<static>|Banner whereMessage($value)
 * @method static Builder<static>|Banner whereTextColor($value)
 * @method static Builder<static>|Banner whereTitle($value)
 * @method static Builder<static>|Banner whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Banner extends Model
{
    use CentralConnection;
    use ClearsResponseCache;

    protected $fillable = [
        'title',
        'message',
        'link_url',
        'link_text',
        'background_color',
        'text_color',
        'created_at',
        'updated_at',
    ];
}
