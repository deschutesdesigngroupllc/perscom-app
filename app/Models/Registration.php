<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * @property int $id
 * @property string $organization
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $verified_at
 * @property int|null $tenant_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Tenant|null $tenant
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registration newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registration newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registration query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registration whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registration whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registration whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registration whereOrganization($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registration whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registration whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registration whereVerifiedAt($value)
 *
 * @mixin \Eloquent
 */
class Registration extends Model
{
    use CentralConnection;
    use Notifiable;

    protected $table = 'registrations';

    protected $fillable = [
        'organization',
        'email',
        'verified_at',
        'tenant_id',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function routeNotificationForMail(): string
    {
        return $this->email;
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
        ];
    }
}
