<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Enums\NotificationChannel;
use App\Observers\MessageObserver;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
use App\Traits\HasSchedule;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $message
 * @property \Illuminate\Support\Collection<int, NotificationChannel>|null $channels
 * @property \Illuminate\Support\Collection|null $recipients
 * @property bool $repeats
 * @property \Illuminate\Support\Carbon|null $send_at
 * @property \Illuminate\Support\Carbon|null $sent_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $label
 * @property-read string|null $relative_url
 * @property-read Schedule|null $schedule
 * @property-read string|null $url
 *
 * @method static \Database\Factories\MessageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereChannels($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereRecipients($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereRepeats($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereSendAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
#[ObservedBy(MessageObserver::class)]
class Message extends Model implements HasLabel
{
    use HasFactory;
    use HasResourceLabel;
    use HasResourceUrl;
    use HasSchedule;

    /**
     * @var null[]
     */
    protected $attributes = [
        'recipients' => null,
    ];

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'message',
        'channels',
        'recipients',
        'repeats',
        'send_at',
        'sent_at',
        'created_at',
        'updated_at',
    ];

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'channels' => AsEnumCollection::of(NotificationChannel::class),
            'recipients' => AsCollection::class,
            'repeats' => 'boolean',
            'send_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }
}
