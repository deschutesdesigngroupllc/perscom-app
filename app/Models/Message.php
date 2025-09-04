<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Enums\NotificationChannel;
use App\Observers\MessageObserver;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
use App\Traits\HasSchedule;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $message
 * @property Collection<int, NotificationChannel>|null $channels
 * @property Collection|null $recipients
 * @property bool $repeats
 * @property Carbon|null $send_at
 * @property Carbon|null $sent_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $label
 * @property-read string|null $relative_url
 * @property-read Schedule|null $schedule
 * @property-read string|null $url
 *
 * @method static \Database\Factories\MessageFactory factory($count = null, $state = [])
 * @method static Builder<static>|Message newModelQuery()
 * @method static Builder<static>|Message newQuery()
 * @method static Builder<static>|Message query()
 * @method static Builder<static>|Message whereChannels($value)
 * @method static Builder<static>|Message whereCreatedAt($value)
 * @method static Builder<static>|Message whereId($value)
 * @method static Builder<static>|Message whereMessage($value)
 * @method static Builder<static>|Message whereRecipients($value)
 * @method static Builder<static>|Message whereRepeats($value)
 * @method static Builder<static>|Message whereSendAt($value)
 * @method static Builder<static>|Message whereSentAt($value)
 * @method static Builder<static>|Message whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
#[ObservedBy(MessageObserver::class)]
class Message extends Model implements HasLabel
{
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasFactory;
    use HasResourceLabel;
    use HasResourceUrl;
    use HasSchedule;

    protected $attributes = [
        'recipients' => null,
    ];

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
