<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issuer extends Model
{
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasFactory;

    protected $fillable = [
        'name',
    ];
}
