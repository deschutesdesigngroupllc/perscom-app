<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\SubmissionObserver;
use App\Traits\CanBeRead;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasComments;
use App\Traits\HasCustomFieldData;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
use App\Traits\HasStatusRecords;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Stringable;

/**
 * @property int $id
 * @property int $form_id
 * @property int $user_id
 * @property array<array-key, mixed>|null $data
 * @property string|null $read_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Comment> $comments
 * @property-read int|null $comments_count
 * @property-read Form $form
 * @property-read string $label
 * @property-read string|null $relative_url
 * @property-read Status|null $status
 * @property-read StatusRecord|null $record
 * @property-read Collection<int, Status> $statuses
 * @property-read int|null $statuses_count
 * @property-read string|null $url
 * @property-read User $user
 *
 * @method static \Database\Factories\SubmissionFactory factory($count = null, $state = [])
 * @method static Builder<static>|Submission newModelQuery()
 * @method static Builder<static>|Submission newQuery()
 * @method static Builder<static>|Submission query()
 * @method static Builder<static>|Submission read()
 * @method static Builder<static>|Submission status(?mixed $statuses)
 * @method static Builder<static>|Submission unread()
 * @method static Builder<static>|Submission whereCreatedAt($value)
 * @method static Builder<static>|Submission whereData($value)
 * @method static Builder<static>|Submission whereFormId($value)
 * @method static Builder<static>|Submission whereId($value)
 * @method static Builder<static>|Submission whereReadAt($value)
 * @method static Builder<static>|Submission whereUpdatedAt($value)
 * @method static Builder<static>|Submission whereUserId($value)
 *
 * @mixin \Eloquent
 */
#[ObservedBy(SubmissionObserver::class)]
class Submission extends Model implements HasLabel, Htmlable, Stringable
{
    use CanBeRead;
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasComments;
    use HasCustomFieldData;
    use HasFactory;
    use HasResourceLabel;
    use HasResourceUrl;
    use HasStatusRecords;

    public function __toString(): string
    {
        return $this->toHtml();
    }

    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($model): void {
            $user = match (true) {
                isset($model->user) => $model->user,
                Auth::guard('web')->check() => Auth::guard('web')->user(),
                Auth::guard('api')->check() => Auth::guard('api')->user(),
                default => null
            };

            if ($user) {
                $model->user()->associate($user);
            }
        });
    }

    /**
     * @return string[]
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'form_id',
            'user_id',
            'read_at',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * @return BelongsTo<Form, $this>
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function toHtml(): string
    {
        return view('models.submission')->with('submission', $this)->render();
    }
}
