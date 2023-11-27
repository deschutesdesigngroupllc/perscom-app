<?php

namespace App\Models;

use App\Models\Scopes\SubmissionScope;
use App\Traits\HasStatuses;
use App\Traits\HasUser;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Actions\Actionable;
use Stancl\VirtualColumn\VirtualColumn;
use Stringable;

/**
 * App\Models\Submission
 *
 * @property int $id
 * @property int $form_id
 * @property int|null $user_id
 * @property array|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Action> $actions
 * @property-read int|null $actions_count
 * @property-read \App\Models\Form $form
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Status> $statuses
 * @property-read int|null $statuses_count
 * @property-read \App\Models\User|null $user
 *
 * @method static \Database\Factories\SubmissionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Submission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Submission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Submission query()
 * @method static \Illuminate\Database\Eloquent\Builder|Submission status(?mixed $statuses)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission user(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereFormId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Submission extends Model implements Htmlable, Stringable
{
    use Actionable;
    use HasFactory;
    use HasStatuses;
    use HasUser;
    use VirtualColumn;

    /**
     * @var string[]
     */
    public $guarded = [];

    /**
     * @var string[]
     */
    protected $with = ['form', 'user', 'statuses'];

    /**
     * @var array<int, string>
     */
    protected $hidden = ['data'];

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

    public static function boot(): void
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

    protected static function booted(): void
    {
        static::addGlobalScope(new SubmissionScope());
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function __toString(): string
    {
        return $this->toHtml();
    }

    public function toHtml(): string
    {
        return view('models.submission')->with('submission', $this)->render();
    }
}
