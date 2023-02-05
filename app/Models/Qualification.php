<?php

namespace App\Models;

use App\Traits\HasImages;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Qualification extends Model
{
    use HasFactory;
    use HasImages;

    /**
     * @var string[]
     */
    protected $fillable = ['name', 'description'];
}
