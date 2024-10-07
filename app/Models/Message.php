<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Enums\NotificationChannel;
use App\Observers\MessageObserver;
use App\Traits\CanBeRepeated;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $message
 * @property AsEnumCollection|null $channels
 * @property AsCollection|null $recipients
 * @property int $repeats
 * @property string|null $send_at
 * @property string|null $sent_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read bool $has_passed
 * @property-read \Carbon\Carbon|null $last_occurrence
 * @property-read \Carbon\Carbon|null $next_occurrence
 * @property-read Repeatable|null $repeatSettings
 *
 * @method static \Database\Factories\MessageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Message onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Message query()
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereChannels($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereRecipients($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereRepeats($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereSendAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Message withoutTrashed()
 *
 * @mixin \Eloquent
 */
#[ObservedBy(MessageObserver::class)]
class Message extends Model
{
    use CanBeRepeated;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'message',
        'channels',
        'recipients',
        'repeats',
        'send_at',
        'sent_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function casts(): array
    {
        return [
            'channels' => AsEnumCollection::of(NotificationChannel::class),
            'recipients' => AsCollection::using(User::class),
        ];
    }
}
