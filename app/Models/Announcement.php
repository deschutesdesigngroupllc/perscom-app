<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\Enableable;
use App\Contracts\SendsModelNotifications;
use App\Traits\CanBeEnabled;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasColorField;
use App\Traits\HasModelNotifications;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string $color
 * @property bool $global
 * @property bool $enabled
 * @property Carbon|null $expires_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $label
 * @property-read Collection<int, ModelNotification> $modelNotifications
 * @property-read int|null $model_notifications_count
 * @property-read string|null $relative_url
 * @property-read string|null $url
 *
 * @method static Builder<static>|Announcement disabled()
 * @method static Builder<static>|Announcement enabled()
 * @method static \Database\Factories\AnnouncementFactory factory($count = null, $state = [])
 * @method static Builder<static>|Announcement global()
 * @method static Builder<static>|Announcement newModelQuery()
 * @method static Builder<static>|Announcement newQuery()
 * @method static Builder<static>|Announcement query()
 * @method static Builder<static>|Announcement whereColor($value)
 * @method static Builder<static>|Announcement whereContent($value)
 * @method static Builder<static>|Announcement whereCreatedAt($value)
 * @method static Builder<static>|Announcement whereEnabled($value)
 * @method static Builder<static>|Announcement whereExpiresAt($value)
 * @method static Builder<static>|Announcement whereGlobal($value)
 * @method static Builder<static>|Announcement whereId($value)
 * @method static Builder<static>|Announcement whereTitle($value)
 * @method static Builder<static>|Announcement whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Announcement extends Model implements Enableable, HasColor, HasLabel, SendsModelNotifications
{
    use CanBeEnabled;
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasColorField;
    use HasFactory;
    use HasModelNotifications;
    use HasResourceLabel;
    use HasResourceUrl;

    protected $attributes = [
        'enabled' => true,
        'global' => false,
    ];

    protected $fillable = [
        'title',
        'content',
        'global',
        'expires_at',
        'created_at',
        'updated_at',
    ];

    public function scopeGlobal(Builder $query): void
    {
        $query->where('global', true);
    }

    /**
     * @return Attribute<string, never>
     */
    public function color(): Attribute
    {
        return Attribute::make(
            get: fn ($value): string => match (Str::startsWith($value, '#')) {
                true => $value,
                default => match ($value) {
                    'success' => '#16a34a',
                    'danger' => '#dc2626',
                    'warning' => '#ca8a04',
                    default => '#2563eb'
                },
            },
        );
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'global' => 'boolean',
            'expires_at' => 'datetime',
        ];
    }
}
