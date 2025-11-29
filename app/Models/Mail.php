<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\MailObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $subject
 * @property string $content
 * @property array<array-key, mixed> $recipients
 * @property array<array-key, mixed>|null $links
 * @property bool $send_now
 * @property Carbon|null $send_at
 * @property Carbon|null $sent_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder<static>|Mail newModelQuery()
 * @method static Builder<static>|Mail newQuery()
 * @method static Builder<static>|Mail query()
 * @method static Builder<static>|Mail whereContent($value)
 * @method static Builder<static>|Mail whereCreatedAt($value)
 * @method static Builder<static>|Mail whereId($value)
 * @method static Builder<static>|Mail whereLinks($value)
 * @method static Builder<static>|Mail whereRecipients($value)
 * @method static Builder<static>|Mail whereSendAt($value)
 * @method static Builder<static>|Mail whereSendNow($value)
 * @method static Builder<static>|Mail whereSentAt($value)
 * @method static Builder<static>|Mail whereSubject($value)
 * @method static Builder<static>|Mail whereUpdatedAt($value)
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

    /**
     * @return array<string, string>
     */
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
