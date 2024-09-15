<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\HasFields;
use App\Observers\UserObserver;
use App\Traits\ClearsResponseCache;
use App\Traits\HasAssignmentRecords;
use App\Traits\HasAwardRecords;
use App\Traits\HasCombatRecords;
use App\Traits\HasCustomFields;
use App\Traits\HasProfilePhoto;
use App\Traits\HasQualificationRecords;
use App\Traits\HasRankRecords;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
use App\Traits\HasServiceRecords;
use App\Traits\HasStatuses;
use App\Traits\JwtClaims;
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
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;
use Stancl\VirtualColumn\VirtualColumn;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property int|null $position_id
 * @property int|null $rank_id
 * @property int|null $specialty_id
 * @property int|null $status_id
 * @property int|null $unit_id
 * @property bool $approved
 * @property mixed|null $password
 * @property string|null $remember_token
 * @property string|null $notes
 * @property Carbon|null $notes_updated_at
 * @property string|null $profile_photo
 * @property string|null $cover_photo
 * @property Carbon|null $last_seen_at
 * @property array|null $data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AssignmentRecord> $assignment_records
 * @property-read int|null $assignment_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AwardRecord> $award_records
 * @property-read int|null $award_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PassportClient> $clients
 * @property-read int|null $clients_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CombatRecord> $combat_records
 * @property-read int|null $combat_records_count
 * @property-read string|null $cover_photo_url
 * @property-read EventRegistration $registration
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Field> $fields
 * @property-read int|null $fields_count
 * @property-read string $label
 * @property-read mixed $last_assignment_change_date
 * @property-read mixed $last_rank_change_date
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read mixed $online
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read Position|null $position
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AssignmentRecord> $primary_assignment_records
 * @property-read int|null $primary_assignment_records_count
 * @property-read string $profile_photo_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\QualificationRecord> $qualification_records
 * @property-read int|null $qualification_records_count
 * @property-read Rank|null $rank
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RankRecord> $rank_records
 * @property-read int|null $rank_records_count
 * @property-read \Illuminate\Support\Optional|string|null|null $relative_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AssignmentRecord> $secondary_assignment_records
 * @property-read int|null $secondary_assignment_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ServiceRecord> $service_records
 * @property-read int|null $service_records_count
 * @property-read Specialty|null $specialty
 * @property-read Status|null $status
 * @property-read StatusRecord $record
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Status> $statuses
 * @property-read int|null $statuses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Submission> $submissions
 * @property-read int|null $submissions_count
 * @property-read TaskAssignment $assignment
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Task> $tasks
 * @property-read int|null $tasks_count
 * @property-read mixed $time_in_assignment
 * @property-read mixed $time_in_grade
 * @property-read mixed $time_in_service
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PassportToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read Unit|null $unit
 * @property-read \Illuminate\Support\Optional|string|null|null $url
 *
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User onlyTrashed()
 * @method static Builder|User orderForRoster()
 * @method static Builder|User permission($permissions, $without = false)
 * @method static Builder|User query()
 * @method static Builder|User role($roles, $guard = null, $without = false)
 * @method static Builder|User status(?mixed $statuses)
 * @method static Builder|User whereApproved($value)
 * @method static Builder|User whereCoverPhoto($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereData($value)
 * @method static Builder|User whereDeletedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereLastSeenAt($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User whereNotes($value)
 * @method static Builder|User whereNotesUpdatedAt($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User wherePositionId($value)
 * @method static Builder|User whereProfilePhoto($value)
 * @method static Builder|User whereRankId($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereSpecialtyId($value)
 * @method static Builder|User whereStatusId($value)
 * @method static Builder|User whereUnitId($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User withTrashed()
 * @method static Builder|User withoutPermission($permissions)
 * @method static Builder|User withoutRole($roles, $guard = null)
 * @method static Builder|User withoutTrashed()
 *
 * @mixin \Eloquent
 */
#[ObservedBy(UserObserver::class)]
class User extends Authenticatable implements FilamentUser, HasAvatar, HasFields, HasLabel, HasName, HasTenants, JWTSubject, MustVerifyEmail
{
    use ClearsResponseCache;
    use HasApiTokens;
    use HasAssignmentRecords;
    use HasAwardRecords;
    use HasCombatRecords;
    use HasCustomFields;
    use HasFactory;
    use HasPermissions;
    use HasProfilePhoto;
    use HasQualificationRecords;
    use HasRankRecords;
    use HasResourceLabel;
    use HasResourceUrl;
    use HasRoles;
    use HasServiceRecords;
    use HasStatuses;
    use JwtClaims;
    use Notifiable;
    use SoftDeletes;
    use VirtualColumn;

    public $guarded = [];

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'position_id',
        'rank_id',
        'specialty_id',
        'status_id',
        'unit_id',
        'approved',
        'password',
        'notes',
        'notes_updated_at',
        'profile_photo',
        'cover_photo',
        'last_seen_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'online',
        'profile_photo_url',
        'cover_photo_url',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'email',
            'email_verified_at',
            'position_id',
            'rank_id',
            'specialty_id',
            'status_id',
            'unit_id',
            'approved',
            'password',
            'remember_token',
            'notes',
            'notes_updated_at',
            'profile_photo',
            'cover_photo',
            'last_seen_at',
            'created_at',
            'updated_at',
            'deleted_at',
        ];
    }

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

    public function scopeOrderForRoster(Builder $query): void
    {
        $query
            ->select('users.*')
            ->leftJoin('ranks', 'ranks.id', '=', 'users.rank_id')
            ->leftJoin('positions', 'positions.id', '=', 'users.position_id')
            ->leftJoin('specialties', 'specialties.id', '=', 'users.specialty_id')
            ->orderBy('ranks.order')
            ->orderBy('positions.order')
            ->orderBy('specialties.order')
            ->orderBy('users.name');
    }

    public function online(): Attribute
    {
        return Attribute::make(
            get: fn () => Cache::tags('users_online')->has("user_online_$this->id")
        )->shouldCache();
    }

    public function coverPhotoUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->cover_photo ? Storage::disk('s3')->url($this->cover_photo) : null
        )->shouldCache();
    }

    public function timeInAssignment(): Attribute
    {
        return Attribute::make(
            get: fn () => optional($this->primary_assignment_records()->first()->created_at ?? null, function ($date) {
                return Carbon::now()->diff($date, true);
            })
        )->shouldCache();
    }

    public function timeInGrade(): Attribute
    {
        return Attribute::make(
            get: fn () => optional($this->rank_records()->first()?->created_at ?? null, function ($date) {
                return Carbon::now()->diff($date, true);
            })
        )->shouldCache();
    }

    public function timeInService(): Attribute
    {
        return Attribute::make(
            get: fn () => Carbon::now()->diff($this->created_at, true)
        )->shouldCache();
    }

    public function lastAssignmentChangeDate(): Attribute
    {
        return Attribute::make(
            get: fn () => optional($this->primary_assignment_records()->latest()->first(), function (?AssignmentRecord $record) {
                return $record->created_at;
            })
        )->shouldCache();
    }

    public function lastRankChangeDate(): Attribute
    {
        return Attribute::make(
            get: fn () => optional($this->rank_records()->latest()->first(), function (?RankRecord $record) {
                return $record->created_at;
            })
        )->shouldCache();
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'events_registrations')
            ->withPivot(['id'])
            ->withTimestamps()
            ->as('registration')
            ->using(EventRegistration::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function rank(): BelongsTo
    {
        return $this->belongsTo(Rank::class);
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'users_tasks')
            ->withPivot(['id', 'task_id', 'user_id', 'assigned_by_id', 'assigned_at', 'due_at', 'completed_at', 'expires_at'])
            ->as('assignment')
            ->using(TaskAssignment::class)
            ->withTimestamps();
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

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
