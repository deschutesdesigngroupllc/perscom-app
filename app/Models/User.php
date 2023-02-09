<?php

namespace App\Models;

use App\Traits\HasStatuses;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Actions\Actionable;
use Laravel\Nova\Auth\Impersonatable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

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
 * @property-read string $online
 * @property-read string $relative_url
 * @property-read mixed|null $time_in_assignment
 * @property-read mixed|null $time_in_grade
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
 * @mixin \Eloquent
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use Actionable;
    use HasApiTokens;
    use HasFactory;
    use HasPermissions;
    use HasRoles;
    use HasStatuses;
    use Impersonatable;
    use Notifiable;

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
        'password',
        'notes',
        'notes_updated_at',
        'last_seen_at',
        'social_id',
        'social_driver',
        'social_token',
        'social_refresh_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'social_token',
        'social_refresh_token',
        'social_id',
        'social_driver',
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
    protected $appends = ['online', 'url', 'relative_url'];

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
    ];

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
     * @return mixed|null
     */
    public function getTimeInAssignmentAttribute()
    {
        return $this->assignment_records()->count() ? Carbon::now()->diff($this->assignment_records()
                                                                               ->latest()
                                                                               ->first()->created_at, CarbonInterface::DIFF_ABSOLUTE, false, 3)
            : null;
    }

    /**
     * @return string
     */
    public function getOnlineAttribute()
    {
        return Cache::tags('user.online')->has("user.online.$this->id");
    }

    /**
     * @return string
     */
    public function getUrlAttribute()
    {
        return route('nova.pages.detail', [
            'resource' => \App\Nova\User::uriKey(),
            'resourceId' => $this->id,
        ]);
    }

    /**
     * @return string
     */
    public function getRelativeUrlAttribute()
    {
        return route('nova.pages.detail', [
            'resource' => \App\Nova\User::uriKey(),
            'resourceId' => $this->id,
        ], false);
    }

    /**
     * @return mixed|null
     */
    public function getTimeInGradeAttribute()
    {
        return $this->rank_records->count() ? Carbon::now()->diff($this->rank_records()
                                                                       ->latest()
                                                                       ->first()->created_at, CarbonInterface::DIFF_ABSOLUTE, false, 3)
            : null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assignment_records()
    {
        return $this->hasMany(AssignmentRecord::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function award_records()
    {
        return $this->hasMany(AwardRecord::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function combat_records()
    {
        return $this->hasMany(CombatRecord::class);
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
    public function qualifications()
    {
        return $this->belongsToMany(Qualification::class, 'records_qualifications')->withPivot(['text'])->as('record');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function qualification_records()
    {
        return $this->hasMany(QualificationRecord::class);
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
        return $this->hasMany(RankRecord::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function service_records()
    {
        return $this->hasMany(ServiceRecord::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
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
        return $this->belongsToMany(Task::class, 'users_tasks', 'task_id')
            ->withPivot(['id', 'assigned_by_id', 'completed_at', 'assigned_at', 'expires_at'])
            ->as('assignment')
            ->using(TaskAssignment::class)
            ->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
