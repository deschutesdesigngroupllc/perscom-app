<?php

namespace App\Models;

use App\Models\Scopes\SubmissionScope;
use App\Traits\HasStatuses;
use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Actions\Actionable;
use Stancl\VirtualColumn\VirtualColumn;

/**
 * App\Models\Submission
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Action> $actions
 * @property-read int|null $actions_count
 * @property-read \App\Models\Form|null $form
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Status> $statuses
 * @property-read int|null $statuses_count
 * @property-read \App\Models\User|null $user
 *
 * @method static \Database\Factories\SubmissionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Submission forUser(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Submission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Submission query()
 *
 * @mixin \Eloquent
 */
class Submission extends Model
{
    use Actionable;
    use HasFactory;
    use HasStatuses;
    use HasUser;
    use VirtualColumn;

    /**
     * @var array
     */
    public $guarded = [];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['form', 'user', 'statuses'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'data',
    ];

    /**
     * @return string[]
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'form_id',
            'user_id',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * Run on boot
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $user = match (true) {
                isset($model->user) => $model->user,
                Auth::guard('web')->check() => Auth::guard('web')->user(),
                Auth::guard('jwt')->check() => Auth::guard('jwt')->user(),
                default => null
            };

            if ($user) {
                $model->user()->associate($user);
            }
        });
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new SubmissionScope());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}
