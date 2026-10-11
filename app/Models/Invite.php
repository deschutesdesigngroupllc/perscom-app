<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Enums\InviteStatus;
use App\Models\Enums\InviteType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use SensitiveParameter;

/**
 * @property int $id
 * @property InviteType $type
 * @property string $email
 * @property string|null $name
 * @property int|null $user_id
 * @property int|null $invited_by_id
 * @property string $token
 * @property Carbon|null $sent_at
 * @property Carbon|null $expires_at
 * @property Carbon|null $accepted_at
 * @property Carbon|null $revoked_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $invitedBy
 * @property-read InviteStatus $status
 * @property-read User|null $user
 *
 * @method static \Database\Factories\InviteFactory factory($count = null, $state = [])
 * @method static Builder<static>|Invite newModelQuery()
 * @method static Builder<static>|Invite newQuery()
 * @method static Builder<static>|Invite pending()
 * @method static Builder<static>|Invite query()
 * @method static Builder<static>|Invite whereAcceptedAt($value)
 * @method static Builder<static>|Invite whereCreatedAt($value)
 * @method static Builder<static>|Invite whereEmail($value)
 * @method static Builder<static>|Invite whereExpiresAt($value)
 * @method static Builder<static>|Invite whereId($value)
 * @method static Builder<static>|Invite whereInvitedById($value)
 * @method static Builder<static>|Invite whereName($value)
 * @method static Builder<static>|Invite whereRevokedAt($value)
 * @method static Builder<static>|Invite whereSentAt($value)
 * @method static Builder<static>|Invite whereToken($value)
 * @method static Builder<static>|Invite whereType($value)
 * @method static Builder<static>|Invite whereUpdatedAt($value)
 * @method static Builder<static>|Invite whereUserId($value)
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
class Invite extends Model
{
    use HasFactory;

    public const int EXPIRATION_DAYS = 7;

    protected $fillable = [
        'type',
        'email',
        'name',
        'user_id',
        'invited_by_id',
        'token',
        'sent_at',
        'expires_at',
        'accepted_at',
        'revoked_at',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'token',
    ];

    public static function hashToken(#[SensitiveParameter] string $token): string
    {
        return hash('sha256', $token);
    }

    public static function findByToken(#[SensitiveParameter] string $token): ?Invite
    {
        return Invite::query()->where('token', Invite::hashToken($token))->first();
    }

    /**
     * Generate a fresh token, reset the expiration window and return the plain-text token. Only the
     * hash is persisted, so the returned value must be delivered to the recipient immediately.
     */
    public function refreshToken(): string
    {
        $token = Str::random(64);

        $this->forceFill([
            'token' => Invite::hashToken($token),
            'sent_at' => now(),
            'expires_at' => now()->addDays(Invite::EXPIRATION_DAYS),
        ])->save();

        return $token;
    }

    public function isPending(): bool
    {
        return $this->status === InviteStatus::PENDING;
    }

    public function revoke(): void
    {
        $this->update([
            'revoked_at' => now(),
        ]);
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function scopePending(Builder $query): void
    {
        $query
            ->whereNull('accepted_at')
            ->whereNull('revoked_at')
            ->where(fn (Builder $query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    }

    /**
     * @return Attribute<InviteStatus, never>
     */
    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn (): InviteStatus => match (true) {
                filled($this->accepted_at) => InviteStatus::ACCEPTED,
                filled($this->revoked_at) => InviteStatus::REVOKED,
                filled($this->expires_at) && $this->expires_at->isPast() => InviteStatus::EXPIRED,
                default => InviteStatus::PENDING,
            }
        );
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => InviteType::class,
            'sent_at' => 'datetime',
            'expires_at' => 'datetime',
            'accepted_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }
}
