<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\MailObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $subject
 * @property string $content
 * @property array $recipients
 * @property array|null $links
 * @property int $send_now
 * @property \Illuminate\Support\Carbon|null $send_at
 * @property \Illuminate\Support\Carbon|null $sent_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @method static \Database\Factories\MailFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Mail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mail onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Mail query()
 * @method static \Illuminate\Database\Eloquent\Builder|Mail whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mail whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mail whereLinks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mail whereRecipients($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mail whereSendAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mail whereSendNow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mail whereSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mail whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mail withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Mail withoutTrashed()
 *
 * @mixin \Eloquent
 */
#[ObservedBy(MailObserver::class)]
class Mail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'mail';

    protected $fillable = [
        'subject',
        'content',
        'links',
        'send_at',
        'sent_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function casts(): array
    {
        return [
            'links' => 'json',
            'recipients' => 'array',
            'send_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }
}
