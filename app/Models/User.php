<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\UserObserver;
use App\Settings\DashboardSettings;
use App\Traits\CanReceiveNotifications;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasAssignmentRecords;
use App\Traits\HasAttachments;
use App\Traits\HasAwardRecords;
use App\Traits\HasCombatRecords;
use App\Traits\HasCustomFieldData;
use App\Traits\HasCustomFields;
use App\Traits\HasPosition;
use App\Traits\HasProfilePhoto;
use App\Traits\HasQualificationRecords;
use App\Traits\HasRank;
use App\Traits\HasRankRecords;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
use App\Traits\HasServiceRecords;
use App\Traits\HasSpecialty;
use App\Traits\HasStatus;
use App\Traits\HasStatusRecords;
use App\Traits\HasTrainingRecords;
use App\Traits\HasUnit;
use App\Traits\JwtClaims;
use App\Traits\SocialRelationships;
use Archilex\AdvancedTables\Concerns\HasViews;
use Archilex\AdvancedTables\Models\ManagedPresetView;
use Archilex\AdvancedTables\Models\UserView;
use Carbon\CarbonInterval;
use Exception;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string|null $phone_number
 * @property int|null $position_id
 * @property int|null $rank_id
 * @property int|null $specialty_id
 * @property int|null $status_id
 * @property int|null $unit_id
 * @property int|null $unit_slot_id
 * @property bool $approved
 * @property string|null $password
 * @property string|null $remember_token
 * @property string|null $notes
 * @property Carbon|null $notes_updated_at
 * @property string|null $profile_photo
 * @property string|null $cover_photo
 * @property Carbon|null $last_seen_at
 * @property string|null $facebook_user_id
 * @property string|null $github_user_id
 * @property string|null $google_user_id
 * @property string|null $discord_user_id
 * @property string|null $discord_private_channel_id
 * @property array<array-key, mixed>|null $data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AssignmentRecord> $assignment_records
 * @property-read int|null $assignment_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Attachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AwardRecord> $award_records
 * @property-read int|null $award_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Award> $awards
 * @property-read int|null $awards_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, PassportClient> $clients
 * @property-read int|null $clients_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CombatRecord> $combat_records
 * @property-read int|null $combat_records_count
 * @property-read string|null $cover_photo_url
 * @property-read bool $discord_connected
 * @property-read SocialiteUser|null $discordUser
 * @property-read EventRegistration|null $registration
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Event> $events
 * @property-read int|null $events_count
 * @property-read bool $facebook_connected
 * @property-read SocialiteUser|null $facebookUser
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Field> $fields
 * @property-read int|null $fields_count
 * @property-read bool $github_connected
 * @property-read SocialiteUser|null $githubUser
 * @property-read bool $google_connected
 * @property-read SocialiteUser|null $googleUser
 * @property-read string $label
 * @property-read Carbon|null $last_assignment_change_date
 * @property-read Carbon|null $last_rank_change_date
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Archilex\AdvancedTables\Models\ManagedDefaultView> $managedDefaultViews
 * @property-read int|null $managed_default_views_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ManagedPresetView> $managedPresetViews
 * @property-read int|null $managed_preset_views_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, UserView> $managedUserViews
 * @property-read int|null $managed_user_views_count
 * @property-read ModelNotification|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $modelNotifications
 * @property-read int|null $model_notifications_count
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read mixed $online
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read Position|null $position
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AssignmentRecord> $primary_assignment_records
 * @property-read int|null $primary_assignment_records_count
 * @property-read string $profile_photo_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, QualificationRecord> $qualification_records
 * @property-read int|null $qualification_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Qualification> $qualifications
 * @property-read int|null $qualifications_count
 * @property-read Rank|null $rank
 * @property-read \Illuminate\Database\Eloquent\Collection<int, RankRecord> $rank_records
 * @property-read int|null $rank_records_count
 * @property-read string|null $relative_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AssignmentRecord> $secondary_assignment_records
 * @property-read int|null $secondary_assignment_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ServiceRecord> $service_records
 * @property-read int|null $service_records_count
 * @property-read Specialty|null $specialty
 * @property-read Status|null $status
 * @property-read StatusRecord|null $record
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Status> $statuses
 * @property-read int|null $statuses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Submission> $submissions
 * @property-read int|null $submissions_count
 * @property-read TaskAssignment|null $assignment
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Task> $tasks
 * @property-read int|null $tasks_count
 * @property-read CarbonInterval|null $time_in_assignment
 * @property-read CarbonInterval|null $time_in_grade
 * @property-read CarbonInterval $time_in_service
 * @property-read \Illuminate\Database\Eloquent\Collection<int, PassportToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, TrainingRecord> $training_records
 * @property-read int|null $training_records_count
 * @property-read Unit|null $unit
 * @property-read UnitSlot|null $unit_slot
 * @property-read string|null $url
 * @property-read \Illuminate\Database\Eloquent\Collection|Credential[] $credentials
 * @property-read int|null $credentials_count
 *
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static Builder<static>|User newModelQuery()
 * @method static Builder<static>|User newQuery()
 * @method static Builder<static>|User orderForRoster()
 * @method static Builder<static>|User permission($permissions, $without = false)
 * @method static Builder<static>|User position(\App\Models\Position $position)
 * @method static Builder<static>|User query()
 * @method static Builder<static>|User rank(\App\Models\Rank $rank)
 * @method static Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static Builder<static>|User specialty(\App\Models\Specialty $specialty)
 * @method static Builder<static>|User status(\App\Models\Status $status)
 * @method static Builder<static>|User unit(\App\Models\Unit $unit)
 * @method static Builder<static>|User whereApproved($value)
 * @method static Builder<static>|User whereCoverPhoto($value)
 * @method static Builder<static>|User whereCreatedAt($value)
 * @method static Builder<static>|User whereData($value)
 * @method static Builder<static>|User whereDiscordPrivateChannelId($value)
 * @method static Builder<static>|User whereDiscordUserId($value)
 * @method static Builder<static>|User whereEmail($value)
 * @method static Builder<static>|User whereEmailVerifiedAt($value)
 * @method static Builder<static>|User whereFacebookUserId($value)
 * @method static Builder<static>|User whereGithubUserId($value)
 * @method static Builder<static>|User whereGoogleUserId($value)
 * @method static Builder<static>|User whereId($value)
 * @method static Builder<static>|User whereLastSeenAt($value)
 * @method static Builder<static>|User whereName($value)
 * @method static Builder<static>|User whereNotes($value)
 * @method static Builder<static>|User whereNotesUpdatedAt($value)
 * @method static Builder<static>|User wherePassword($value)
 * @method static Builder<static>|User wherePhoneNumber($value)
 * @method static Builder<static>|User wherePositionId($value)
 * @method static Builder<static>|User whereProfilePhoto($value)
 * @method static Builder<static>|User whereRankId($value)
 * @method static Builder<static>|User whereRememberToken($value)
 * @method static Builder<static>|User whereSpecialtyId($value)
 * @method static Builder<static>|User whereStatusId($value)
 * @method static Builder<static>|User whereUnitId($value)
 * @method static Builder<static>|User whereUnitSlotId($value)
 * @method static Builder<static>|User whereUpdatedAt($value)
 * @method static Builder<static>|User withoutPermission($permissions)
 * @method static Builder<static>|User withoutRole($roles, $guard = null)
 *
 * @mixin \Eloquent
 */
#[ObservedBy(UserObserver::class)]
class User extends Authenticatable implements FilamentUser, HasAvatar, HasLabel, HasName, HasTenants, JWTSubject, MustVerifyEmail
{
    use CanReceiveNotifications;
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasApiTokens;
    use HasAssignmentRecords;
    use HasAttachments;
    use HasAwardRecords;
    use HasCombatRecords;
    use HasCustomFieldData;
    use HasCustomFields;
    use HasFactory;
    use HasPermissions;
    use HasPosition;
    use HasProfilePhoto;
    use HasQualificationRecords;
    use HasRank;
    use HasRankRecords;
    use HasResourceLabel;
    use HasResourceUrl;
    use HasRoles;
    use HasServiceRecords;
    use HasSpecialty;
    use HasStatus, HasStatusRecords {
        HasStatus::status insteadof HasStatusRecords;
        HasStatus::scopeStatus insteadof HasStatusRecords;
    }
    use HasTrainingRecords;
    use HasUnit;
    use HasViews;
    use JwtClaims;
    use Notifiable;
    use SocialRelationships;

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'phone_number',
        'unit_slot_id',
        'approved',
        'password',
        'notes',
        'notes_updated_at',
        'profile_photo',
        'cover_photo',
        'last_seen_at',
        'facebook_user_id',
        'github_user_id',
        'google_user_id',
        'discord_user_id',
        'discord_private_channel_id',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'discord_private_channel_id',
    ];

    protected $appends = [
        'last_assignment_change_date',
        'last_rank_change_date',
        'online',
        'profile_photo_url',
        'cover_photo_url',
    ];

    /**
     * @return string[]
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'email',
            'email_verified_at',
            'phone_number',
            'position_id',
            'rank_id',
            'specialty_id',
            'status_id',
            'unit_id',
            'unit_slot_id',
            'approved',
            'password',
            'remember_token',
            'notes',
            'notes_updated_at',
            'profile_photo',
            'cover_photo',
            'last_seen_at',
            'facebook_user_id',
            'github_user_id',
            'google_user_id',
            'discord_user_id',
            'discord_private_channel_id',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * @return array<int, Tenant>|Collection<int, Tenant>
     */
    public function getTenants(Panel $panel): array|Collection
    {
        return Collection::wrap(tenant());
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return true;
    }

    /**
     * @throws Exception
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'app';
    }

    public function canImpersonate(): bool
    {
        return true;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->profile_photo_url;
    }

    public function getFilamentName(): string
    {
        return $this->name;
    }

    public function routeNotificationForDiscord(): ?string
    {
        return $this->discord_private_channel_id;
    }

    public function routeNotificationForTwilio(): ?string
    {
        return $this->phone_number;
    }

    public function scopeOrderForRoster(Builder $query): void
    {
        /** @var DashboardSettings $settings */
        $settings = app(DashboardSettings::class);

        $query
            ->select('users.*')
            ->leftJoin('ranks', 'ranks.id', '=', 'users.rank_id')
            ->leftJoin('positions', 'positions.id', '=', 'users.position_id')
            ->leftJoin('specialties', 'specialties.id', '=', 'users.specialty_id');

        collect($settings->roster_sort_order)->each(fn ($column) => $query->orderBy($column));
    }

    /**
     * @return Attribute<string, never>
     */
    public function online(): Attribute
    {
        return Attribute::make(
            get: fn () => Cache::tags('users_online')->has("user_online_$this->id")
        )->shouldCache();
    }

    /**
     * @return Attribute<?string, never>
     */
    public function coverPhotoUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->cover_photo ? Storage::url($this->cover_photo) : null
        )->shouldCache();
    }

    /**
     * @return Attribute<?CarbonInterval, never>
     */
    public function timeInAssignment(): Attribute
    {
        return Attribute::make(
            get: fn (): ?CarbonInterval => optional($this->primary_assignment_records()->first()->created_at ?? null, fn ($date): CarbonInterval => Carbon::now()->diff($date, true))
        )->shouldCache();
    }

    /**
     * @return Attribute<?CarbonInterval, never>
     */
    public function timeInGrade(): Attribute
    {
        return Attribute::make(
            get: fn (): ?CarbonInterval => optional($this->rank_records()->first()?->created_at ?? null, fn ($date): CarbonInterval => Carbon::now()->diff($date, true))
        )->shouldCache();
    }

    /**
     * @return Attribute<CarbonInterval, never>
     */
    public function timeInService(): Attribute
    {
        return Attribute::make(
            get: fn (): CarbonInterval => Carbon::now()->diff($this->created_at, true)
        )->shouldCache();
    }

    /**
     * @return Attribute<?Carbon, never>
     */
    public function lastAssignmentChangeDate(): Attribute
    {
        return Attribute::make(
            get: function (): ?Carbon {
                /** @var ?AssignmentRecord $record */
                $record = $this->primary_assignment_records()->latest()->first();

                return $record->created_at ?? null;
            }
        )->shouldCache();
    }

    /**
     * @return Attribute<?Carbon, never>
     */
    public function lastRankChangeDate(): Attribute
    {
        return Attribute::make(
            get: function (): ?Carbon {
                /** @var ?RankRecord $record */
                $record = $this->rank_records()->latest()->first();

                return $record->created_at ?? null;
            }
        )->shouldCache();
    }

    /**
     * @return BelongsToMany<Event, $this>
     */
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'events_registrations')
            ->withPivot(['id', 'status'])
            ->withTimestamps()
            ->as('registration')
            ->using(EventRegistration::class);
    }

    /**
     * @return HasMany<Submission, $this>
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * @return BelongsToMany<Task, $this>
     */
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'users_tasks')
            ->withPivot(['id', 'task_id', 'user_id', 'assigned_by_id', 'assigned_at', 'due_at', 'completed_at', 'expires_at'])
            ->as('assignment')
            ->using(TaskAssignment::class)
            ->withTimestamps();
    }

    /**
     * @return BelongsTo<UnitSlot, $this>
     */
    public function unit_slot(): BelongsTo
    {
        return $this->belongsTo(UnitSlot::class);
    }

    protected function getDefaultGuardName(): string
    {
        return 'web';
    }

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'approved' => 'boolean',
            'email_verified_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'notes_updated_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
