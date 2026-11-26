<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Enums\OrganizationType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
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
 * @property string|null $name
 * @property string $email
 * @property string|null $subdomain
 * @property OrganizationType|null $organization_type
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
 * @method static Builder<static>|Registration whereName($value)
 * @method static Builder<static>|Registration whereOrganization($value)
 * @method static Builder<static>|Registration whereOrganizationType($value)
 * @method static Builder<static>|Registration whereSubdomain($value)
 * @method static Builder<static>|Registration whereTenantId($value)
 * @method static Builder<static>|Registration whereUpdatedAt($value)
 * @method static Builder<static>|Registration whereVerifiedAt($value)
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
#[Fillable([
    'organization',
    'name',
    'email',
    'subdomain',
    'organization_type',
    'verified_at',
    'tenant_id',
])]
#[Table(name: 'registrations')]
class Registration extends Model
{
    use CentralConnection;
    use Notifiable;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'organization_type' => OrganizationType::class,
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function routeNotificationForMail(): string
    {
        return $this->email;
    }
}
