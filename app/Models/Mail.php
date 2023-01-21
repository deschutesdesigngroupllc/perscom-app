<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class Mail extends Model
{
    use CentralConnection;
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
        'send_at' => 'datetime',
        'sent_at' => 'datetime'
    ];
}
