<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ModelNotification extends Model
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
    ];

    /**
     * @return array<string, string|int>
     */
    public static function forUser(User|string|int $user, string $event, string $subject, string $message): array
    {
        if (is_string($user) || is_int($user)) {
            $user = User::findOrFail($user);
        }

        return [
            'user_type' => get_class($user),
            'user_id' => $user->id,
            'event' => $event,
            'subject' => $subject,
            'message' => $message,
        ];
    }

    /**
     * @return array<string, string|int>
     */
    public static function forGroup(Group|string|int $group, string $event, string $subject, string $message): array
    {
        if (is_string($group) || is_int($group)) {
            $group = Group::findOrFail($group);
        }

        return [
            'group_type' => get_class($group),
            'group_id' => $group->id,
            'event' => $event,
            'subject' => $subject,
            'message' => $message,
        ];
    }

    /**
     * @return array<string, string|int>
     */
    public static function forUnit(Unit|string|int $unit, string $event, string $subject, string $message): array
    {
        if (is_string($unit) || is_int($unit)) {
            $unit = Unit::findOrFail($unit);
        }

        return [
            'unit_type' => get_class($unit),
            'unit_id' => $unit->id,
            'event' => $event,
            'subject' => $subject,
            'message' => $message,
        ];
    }

    /**
     * @return MorphTo<Model>
     */
    public function model(): MorphTo
    {
        return $this->morphTo('model');
    }

    /**
     * @return MorphTo<User>
     */
    public function group(): MorphTo
    {
        return $this->morphTo('user');
    }

    /**
     * @return MorphTo<Unit>
     */
    public function unit(): MorphTo
    {
        return $this->morphTo('unit');
    }

    /**
     * @return MorphTo<Group>
     */
    public function user(): MorphTo
    {
        return $this->morphTo('group');
    }
}
