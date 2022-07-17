<?php

namespace App\Models;

use App\Models\Records\Assignment as AssignmentRecords;
use App\Models\Records\Award as AwardRecords;
use App\Models\Records\Combat as CombatRecords;
use App\Models\Records\Qualification as QualificationRecords;
use App\Models\Records\Rank as RankRecords;
use App\Models\Records\Service as ServiceRecords;
use App\Traits\HasStatuses;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Actions\Actionable;

class Person extends Model
{
    use Actionable;
    use HasFactory;
    use HasStatuses;

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['assignment', 'full_name', 'rank'];

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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'people_users');
    }
}
