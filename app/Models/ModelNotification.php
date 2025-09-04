<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Enums\NotificationChannel;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $model_type
 * @property int $model_id
 * @property string|null $group_type
 * @property int|null $group_id
 * @property string|null $unit_type
 * @property int|null $unit_id
 * @property string|null $user_type
 * @property int|null $user_id
 * @property string|null $event
 * @property string|null $subject
 * @property string|null $message
 * @property Collection<int, NotificationChannel>|null $channels
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|Eloquent|null $group
 * @property-read Model|Eloquent $model
 * @property-read Model|Eloquent|null $unit
 * @property-read Model|Eloquent|null $user
 *
 * @method static Builder<static>|ModelNotification newModelQuery()
 * @method static Builder<static>|ModelNotification newQuery()
 * @method static Builder<static>|ModelNotification query()
 * @method static Builder<static>|ModelNotification whereChannels($value)
 * @method static Builder<static>|ModelNotification whereCreatedAt($value)
 * @method static Builder<static>|ModelNotification whereEvent($value)
 * @method static Builder<static>|ModelNotification whereGroupId($value)
 * @method static Builder<static>|ModelNotification whereGroupType($value)
 * @method static Builder<static>|ModelNotification whereId($value)
 * @method static Builder<static>|ModelNotification whereMessage($value)
 * @method static Builder<static>|ModelNotification whereModelId($value)
 * @method static Builder<static>|ModelNotification whereModelType($value)
 * @method static Builder<static>|ModelNotification whereSubject($value)
 * @method static Builder<static>|ModelNotification whereUnitId($value)
 * @method static Builder<static>|ModelNotification whereUnitType($value)
 * @method static Builder<static>|ModelNotification whereUpdatedAt($value)
 * @method static Builder<static>|ModelNotification whereUserId($value)
 * @method static Builder<static>|ModelNotification whereUserType($value)
 *
 * @mixin Eloquent
 */
class ModelNotification extends MorphPivot
{
    protected $table = 'model_has_notifications';

    protected $fillable = [
        'model_type',
        'model_id',
        'group_type',
        'group_id',
        'unit_type',
        'unit_id',
        'user_type',
        'user_id',
        'event',
        'subject',
        'message',
        'channels',
    ];

    /**
     * @param  array<NotificationChannel|string>  $channels
     * @return array<string, mixed>
     */
    public static function forUser(User|string|int $user, string $event, string $subject, string $message, array $channels): array
    {
        if (is_string($user) || is_int($user)) {
            $user = User::findOrFail($user);
        }

        return [
            'user_type' => $user::class,
            'user_id' => $user->id,
            'event' => $event,
            'subject' => $subject,
            'message' => $message,
            'channels' => $channels,
        ];
    }

    /**
     * @param  array<NotificationChannel|string>  $channels
     * @return array<string, mixed>
     */
    public static function forGroup(Group|string|int $group, string $event, string $subject, string $message, array $channels): array
    {
        if (is_string($group) || is_int($group)) {
            $group = Group::findOrFail($group);
        }

        return [
            'group_type' => $group::class,
            'group_id' => $group->id,
            'event' => $event,
            'subject' => $subject,
            'message' => $message,
            'channels' => $channels,
        ];
    }

    /**
     * @param  array<NotificationChannel|string>  $channels
     * @return array<string, mixed>
     */
    public static function forUnit(Unit|string|int $unit, string $event, string $subject, string $message, array $channels): array
    {
        if (is_string($unit) || is_int($unit)) {
            $unit = Unit::findOrFail($unit);
        }

        return [
            'unit_type' => $unit::class,
            'unit_id' => $unit->id,
            'event' => $event,
            'subject' => $subject,
            'message' => $message,
            'channels' => $channels,
        ];
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function group(): MorphTo
    {
        return $this->morphTo();
    }

    public function unit(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): MorphTo
    {
        return $this->morphTo('user', 'user_type', 'user_id');
    }

    /**
     * @return Collection<int, User>
     */
    public function getRecipients(): Collection
    {
        if (filled($this->group)) {
            /** @var Group $group */
            $group = $this->group;

            return User::whereIn('unit_id', $group->units->pluck('id'))->get();
        }

        if (filled($this->unit)) {
            /** @var Unit $unit */
            $unit = $this->unit;

            return User::whereUnitId($unit->getKey())->get();
        }

        if (filled($this->user)) {
            /** @var User $user */
            $user = $this->user;

            return Collection::wrap($user);
        }

        return collect();
    }

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'channels' => AsEnumCollection::of(NotificationChannel::class),
        ];
    }
}
