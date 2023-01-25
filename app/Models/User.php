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
            ->withPivot(['assigned_by_id', 'completed_at', 'assigned_at', 'expires_at'])
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
