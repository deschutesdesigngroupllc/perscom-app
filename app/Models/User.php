<?php

namespace App\Models;

use App\Models\Forms\Submission;
use App\Models\Records\Assignment as AssignmentRecords;
use App\Models\Records\Award as AwardRecords;
use App\Models\Records\Combat as CombatRecords;
use App\Models\Records\Qualification as QualificationRecords;
use App\Models\Records\Rank as RankRecords;
use App\Models\Records\Service as ServiceRecords;
use App\Traits\HasStatuses;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
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
        'password',
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
    protected $hidden = ['password', 'remember_token', 'social_token', 'social_refresh_token'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['assignment', 'rank', 'online'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'online' => 'boolean',
    ];

    /**
     * Determine if the user can impersonate another user.
     *
     * @return bool
     */
    public function canImpersonate()
    {
        return $this->hasPermissionTo('impersonate:user');
    }

    /**
     * @return Model|\Illuminate\Database\Eloquent\Relations\HasMany|object|null
     */
    public function getAssignmentAttribute()
    {
        return $this->assignment_records()
            ->latest()
            ->first();
    }

    /**
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "$this->first_name $this->last_name";
    }

    /**
     * @return string
     */
    public function getOnlineAttribute()
    {
        return Cache::tags('user.online')->has("user.online.$this->id");
    }

    /**
     * @return mixed|null
     */
    public function getRankAttribute()
    {
        return $this->ranks()
            ->orderByPivot('created_at', 'desc')
            ->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assignment_records()
    {
        return $this->hasMany(AssignmentRecords::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function award_records()
    {
        return $this->hasMany(AwardRecords::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function combat_records()
    {
        return $this->hasMany(CombatRecords::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function qualifications()
    {
        return $this->belongsToMany(Qualification::class, 'records_qualifications')
            ->withPivot(['text'])
            ->as('record');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function qualification_records()
    {
        return $this->hasMany(QualificationRecords::class);
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
        return $this->hasMany(RankRecords::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function service_records()
    {
        return $this->hasMany(ServiceRecords::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}
