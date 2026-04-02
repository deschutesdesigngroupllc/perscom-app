<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * @property int $id
 * @property string $organization
 * @property string $email
 * @property Carbon|null $verified_at
 * @property int|null $tenant_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Tenant|null $tenant
 *
 * @method static Builder<static>|Registration newModelQuery()
 * @method static Builder<static>|Registration newQuery()
 * @method static Builder<static>|Registration query()
 * @method static Builder<static>|Registration whereCreatedAt($value)
 * @method static Builder<static>|Registration whereEmail($value)
 * @method static Builder<static>|Registration whereId($value)
 * @method static Builder<static>|Registration whereOrganization($value)
 * @method static Builder<static>|Registration whereTenantId($value)
 * @method static Builder<static>|Registration whereUpdatedAt($value)
 * @method static Builder<static>|Registration whereVerifiedAt($value)
 *
 * @mixin Model
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
