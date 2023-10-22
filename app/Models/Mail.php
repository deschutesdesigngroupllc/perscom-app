<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Mail
 *
 * @property int $id
 * @property string $subject
 * @property string $content
 * @property array|null $links
 * @property int $send_now
 * @property \Illuminate\Support\Carbon|null $send_at
 * @property \Illuminate\Support\Carbon|null $sent_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Database\Factories\MailFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Mail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mail query()
 * @method static \Illuminate\Database\Eloquent\Builder|Mail whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mail whereLinks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mail whereSendAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mail whereSendNow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mail whereSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mail whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mail whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Mail extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'mail';

    /**
     * @var string[]
     */
    protected $fillable = ['subject', 'content', 'links', 'send_at', 'sent_at'];

    /**
     * @var string[]
     */
    protected $casts = [
        'links' => 'json',
        'recipients' => 'array',
        'send_at' => 'datetime',
        'sent_at' => 'datetime',
    ];
}
