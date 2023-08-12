<?php

namespace App\Models;

use App\Traits\HasFields;
use App\Traits\HasHiddenFieldAttributes;
use App\Traits\HasResourceUrlAttribute;
use App\Traits\HasStatuses;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Field> $fields
 * @property-read int|null $fields_count
 * @property-read string|null $cover_photo_url
 * @property-read bool $online
 * @property-read string|null $profile_photo_url
 * @property-read string $relative_url
 * @property-read \Illuminate\Support\Optional|mixed $time_in_assignment
 * @property-read \Illuminate\Support\Optional|mixed $time_in_grade
 * @property-read string $url
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \App\Models\Position|null $position
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\QualificationRecord> $qualification_records
 * @property-read int|null $qualification_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Qualification> $qualifications
 * @property-read int|null $qualifications_count
 * @property-read \App\Models\Rank|null $rank
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RankRecord> $rank_records
 * @property-read int|null $rank_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Rank> $ranks
 * @property-read int|null $ranks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Position> $secondary_positions
 * @property-read int|null $secondary_positions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Specialty> $secondary_specialties
 * @property-read int|null $secondary_specialties_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Unit> $secondary_units
 * @property-read int|null $secondary_units_count
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PassportToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \App\Models\Unit|null $unit
 *
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null)
 *
 * @mixin \Eloquent
 */
class User extends Authenticatable implements MustVerifyEmail, JWTSubject
{
    use Actionable;
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
    use VirtualColumn;

    /**
     * @var array
     */
    public $guarded = [];

    /**
     * The attributes that are mass assignable.
     *
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
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
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
    protected $with = ['position', 'specialty', 'rank', 'status', 'unit', 'tasks'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['online', 'url', 'relative_url', 'profile_photo_url', 'cover_photo_url'];

    /**
     * The attributes that should be cast.
     *
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

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'tenant' => tenant()->getTenantKey(),
        ];
    }

    /**
     * Determine if the user can impersonate another user.
     *
     * @return bool
     */
    public function canImpersonate()
    {
        return Gate::check('impersonate', $this);
    }

    /**
     * @return \Illuminate\Support\Optional|mixed
     */
    public function getTimeInAssignmentAttribute()
    {
        return optional($this->assignment_records->first()?->created_at, function ($date) {
            return Carbon::now()->diff($date, true);
        });
    }

    /**
     * @return bool
     */
    public function getOnlineAttribute()
    {
        return Cache::tags('user.online')->has("user.online.$this->id");
    }

    /**
     * @return string|null
     */
    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo ? Storage::url($this->profile_photo) : null;
    }

    /**
     * @return string|null
     */
    public function getCoverPhotoUrlAttribute()
    {
        return $this->cover_photo ? Storage::url($this->cover_photo) : null;
    }

    /**
     * @return \Illuminate\Support\Optional|mixed
     */
    public function getTimeInGradeAttribute()
    {
        return optional($this->rank_records->first()?->created_at, function ($date) {
            return Carbon::now()->diff($date, true);
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assignment_records()
    {
        return $this->hasMany(AssignmentRecord::class)->latest();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function award_records()
    {
        return $this->hasMany(AwardRecord::class)->latest();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function combat_records()
    {
        return $this->hasMany(CombatRecord::class)->latest();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function events()
    {
        return $this->belongsToMany(Event::class, 'events_registrations')
            ->withPivot(['id'])
            ->withTimestamps()
            ->as('registration')
            ->using(EventRegistration::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function secondary_positions()
    {
        return $this->belongsToMany(Position::class, 'users_positions')
            ->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function qualifications()
    {
        return $this->belongsToMany(Qualification::class, 'records_qualifications')->withPivot(['text'])->as('record');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function qualification_records()
    {
        return $this->hasMany(QualificationRecord::class)->latest();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rank()
    {
        return $this->belongsTo(Rank::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function ranks()
    {
        return $this->belongsToMany(Rank::class, 'records_ranks')
            ->withTimestamps()
            ->withPivot(['text', 'type'])
            ->as('record');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rank_records()
    {
        return $this->hasMany(RankRecord::class)->latest();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function service_records()
    {
        return $this->hasMany(ServiceRecord::class)->latest();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function secondary_specialties()
    {
        return $this->belongsToMany(Specialty::class, 'users_specialties')
            ->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'users_tasks', 'task_id')->withPivot([
            'id',
            'assigned_by_id',
            'completed_at',
            'assigned_at',
            'expires_at',
        ])->latest()->as('assignment')->using(TaskAssignment::class)->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function secondary_units()
    {
        return $this->belongsToMany(Unit::class, 'users_units')
            ->withTimestamps();
    }
}
