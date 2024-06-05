<?php

namespace App\Models;

use App\Models\Enums\AssignmentRecordType;
use App\Traits\ClearsResponseCache;
use App\Traits\HasFields;
use App\Traits\HasHiddenFieldAttributes;
use App\Traits\HasResourceUrlAttribute;
use App\Traits\HasStatuses;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Actions\Actionable;
use Laravel\Nova\Auth\Impersonatable;
use Laravel\Passport\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;
use Stancl\VirtualColumn\VirtualColumn;

/**
 * App\Models\User
 *
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
 * @property string|null $password
 * @property string|null $remember_token
 * @property string|null $notes
 * @property Carbon|null $notes_updated_at
 * @property string|null $profile_photo
 * @property string|null $cover_photo
 * @property string|null $social_id
 * @property string|null $social_driver
 * @property string|null $social_token
 * @property string|null $social_refresh_token
 * @property Carbon|null $last_seen_at
 * @property array|null $data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Action> $actions
 * @property-read int|null $actions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AssignmentRecord> $assignment_records
 * @property-read int|null $assignment_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AwardRecord> $award_records
 * @property-read int|null $award_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PassportClient> $clients
 * @property-read int|null $clients_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CombatRecord> $combat_records
 * @property-read int|null $combat_records_count
 * @property-read string|null $cover_photo_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Field> $fields
 * @property-read int|null $fields_count
 * @property-read bool $online
 * @property-read mixed $last_assignment_change_date
 * @property-read mixed $last_rank_change_date
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \App\Models\Position|null $position
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AssignmentRecord> $primary_assignment_records
 * @property-read int|null $primary_assignment_records_count
 * @property-read string|null $profile_photo_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\QualificationRecord> $qualification_records
 * @property-read int|null $qualification_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Qualification> $qualifications
 * @property-read int|null $qualifications_count
 * @property-read \App\Models\Rank|null $rank
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RankRecord> $rank_records
 * @property-read int|null $rank_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Rank> $ranks
 * @property-read int|null $ranks_count
 * @property-read string|null $relative_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AssignmentRecord> $secondary_assignment_records
 * @property-read int|null $secondary_assignment_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ServiceRecord> $service_records
 * @property-read int|null $service_records_count
 * @property-read \App\Models\Specialty|null $specialty
 * @property-read \App\Models\Status|null $status
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Status> $statuses
 * @property-read int|null $statuses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Submission> $submissions
 * @property-read int|null $submissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Task> $tasks
 * @property-read int|null $tasks_count
 * @property-read mixed $time_in_assignment
 * @property-read mixed $time_in_grade
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PassportToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \App\Models\Unit|null $unit
 * @property-read string|null $url
 *
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder|User status(?mixed $statuses)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCoverPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastSeenAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNotesUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereProfilePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSocialDriver($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSocialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSocialRefreshToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSocialToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSpecialtyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutRole($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutTrashed()
 *
 * @mixin \Eloquent
 */
class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use Actionable;
    use ClearsResponseCache;
    use HasApiTokens;
    use HasFactory;
    use HasFields;
    use HasHiddenFieldAttributes;
    use HasPermissions;
    use HasResourceUrlAttribute;
    use HasRoles;
    use HasStatuses;
    use Impersonatable;
    use Notifiable;
    use SoftDeletes;
    use VirtualColumn;

    /**
     * @var string[]
     */
    public $guarded = [];

    /**
     * @var array<int, string>
     */
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
        'social_id',
        'social_driver',
        'social_token',
        'social_refresh_token',
        'last_seen_at',
        'updated_at',
        'created_at',
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [
        'notes',
        'notes_updated_at',
        'password',
        'remember_token',
        'social_token',
        'social_refresh_token',
        'social_id',
        'social_driver',
        'data',
    ];

    /**
     * @var string[]
     */
    protected $with = ['assignment_records', 'award_records', 'combat_records', 'position', 'qualification_records', 'rank', 'rank_records', 'service_records', 'specialty', 'status', 'tasks', 'unit'];

    /**
     * @var array<int, string>
     */
    protected $appends = [
        'online',
        'url',
        'relative_url',
        'profile_photo_url',
        'cover_photo_url',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'notes_updated_at' => 'datetime',
        'online' => 'boolean',
        'approved' => 'boolean',
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
            'social_id',
            'social_driver',
            'social_token',
            'social_refresh_token',
            'last_seen_at',
            'created_at',
            'updated_at',
        ];
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * @return array<string, mixed>
     */
    public function getJWTCustomClaims(): array
    {
        return [
            'name' => $this->name,
            'preferred_username' => $this->email,
            'profile' => $this->url,
            'email' => $this->email,
            'email_verified' => $this->hasVerifiedEmail(),
            'picture' => $this->profile_photo_url,
            'tenant_name' => tenant('name'),
            'tenant_sub' => tenant()->getTenantKey(),
            'locale' => config('app.locale'),
            'zoneinfo' => setting('timezone', config('app.timezone')),
            'updated_at' => Carbon::parse($this->updated_at)->getTimestamp(),
        ];
    }

    public function canImpersonate(): bool
    {
        return Gate::check('impersonate', $this);
    }

    protected function getDefaultGuardName(): string
    {
        return 'web';
    }

    public function timeInAssignment(): Attribute
    {
        return Attribute::make(
            get: fn () => optional($this->primary_assignment_records()->first()->created_at ?? null, function ($date) {
                return Carbon::now()->diff($date, true);
            })
        )->shouldCache();
    }

    public function getOnlineAttribute(): bool
    {
        return Cache::tags('users_online')->has("user_online_$this->id");
    }

    public function profilePhotoUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->profile_photo ? Storage::url($this->profile_photo) : null
        );
    }

    public function coverPhotoUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->cover_photo ? Storage::url($this->cover_photo) : null
        );
    }

    public function timeInGrade(): Attribute
    {
        return Attribute::make(
            get: fn () => optional($this->rank_records()->first()?->created_at ?? null, function ($date) {
                return Carbon::now()->diff($date, true);
            })
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

    public function assignment_records(): HasMany
    {
        return $this->hasMany(AssignmentRecord::class);
    }

    public function primary_assignment_records(): HasMany
    {
        return $this->assignment_records()->where('type', AssignmentRecordType::PRIMARY);
    }

    public function secondary_assignment_records(): HasMany
    {
        return $this->assignment_records()->where('type', AssignmentRecordType::SECONDARY);
    }

    public function award_records(): HasMany
    {
        return $this->hasMany(AwardRecord::class);
    }

    public function combat_records(): HasMany
    {
        return $this->hasMany(CombatRecord::class);
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'events_registrations')
            ->withPivot(['id'])
            ->withTimestamps()
            ->as('registration')
            ->using(EventRegistration::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function qualifications(): BelongsToMany
    {
        return $this->belongsToMany(Qualification::class, 'records_qualifications')->withPivot(['text'])->as('record');
    }

    public function qualification_records(): HasMany
    {
        return $this->hasMany(QualificationRecord::class);
    }

    public function rank(): BelongsTo
    {
        return $this->belongsTo(Rank::class);
    }

    public function ranks(): BelongsToMany
    {
        return $this->belongsToMany(Rank::class, 'records_ranks')
            ->withTimestamps()
            ->withPivot(['text', 'type'])
            ->as('record');
    }

    public function rank_records(): HasMany
    {
        return $this->hasMany(RankRecord::class);
    }

    public function service_records(): HasMany
    {
        return $this->hasMany(ServiceRecord::class);
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
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
}
