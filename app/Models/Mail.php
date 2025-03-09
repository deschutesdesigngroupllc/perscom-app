<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\MailObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $subject
 * @property string $content
 * @property array<array-key, mixed> $recipients
 * @property array<array-key, mixed>|null $links
 * @property bool $send_now
 * @property \Illuminate\Support\Carbon|null $send_at
 * @property string|null $sent_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mail whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mail whereLinks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mail whereRecipients($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mail whereSendAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mail whereSendNow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mail whereSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mail whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mail whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
#[ObservedBy(MailObserver::class)]
class Mail extends Model
{
    protected $table = 'mail';

    protected $fillable = [
        'subject',
        'content',
        'recipients',
        'links',
        'send_now',
        'send_at',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'recipients' => 'json',
            'links' => 'json',
            'send_now' => 'boolean',
            'send_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }
}
