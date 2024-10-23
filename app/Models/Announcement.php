<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\Enableable;
use App\Traits\CanBeEnabled;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasColorField;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string $color
 * @property bool $global
 * @property bool $enabled
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read string $label
 * @property-read \Illuminate\Support\Optional|string|null|null $relative_url
 * @property-read \Illuminate\Support\Optional|string|null|null $url
 *
 * @method static Builder|Announcement disabled()
 * @method static Builder|Announcement enabled()
 * @method static \Database\Factories\AnnouncementFactory factory($count = null, $state = [])
 * @method static Builder|Announcement global()
 * @method static Builder|Announcement newModelQuery()
 * @method static Builder|Announcement newQuery()
 * @method static Builder|Announcement onlyTrashed()
 * @method static Builder|Announcement query()
 * @method static Builder|Announcement whereColor($value)
 * @method static Builder|Announcement whereContent($value)
 * @method static Builder|Announcement whereCreatedAt($value)
 * @method static Builder|Announcement whereDeletedAt($value)
 * @method static Builder|Announcement whereEnabled($value)
 * @method static Builder|Announcement whereExpiresAt($value)
 * @method static Builder|Announcement whereGlobal($value)
 * @method static Builder|Announcement whereId($value)
 * @method static Builder|Announcement whereTitle($value)
 * @method static Builder|Announcement whereUpdatedAt($value)
 * @method static Builder|Announcement withTrashed()
 * @method static Builder|Announcement withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Announcement extends Model implements Enableable, HasColor, HasLabel
{
    use CanBeEnabled;
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasColorField;
    use HasFactory;
    use HasResourceLabel;
    use HasResourceUrl;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'content',
        'global',
        'expires_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function scopeGlobal(Builder $query): void
    {
        $query->where('global', true);
    }

    public function color(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => match (Str::startsWith($value, '#')) {
                true => $value,
                default => match ($value) {
                    'info' => '#2563eb',
                    'success' => '#16a34a',
                    'danger' => '#dc2626',
                    'warning' => '#ca8a04'
                },
            },
        );
    }

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'global' => 'boolean',
            'expires_at' => 'datetime',
        ];
    }
}
