<?php

namespace App\Models;

use App\Traits\HasImages;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Award extends Model
{
    use HasFactory;
    use HasImages;

    /**
     * @var string[]
     */
    protected $fillable = ['name', 'description'];

    /**
     * @var string[]
     */
    protected $with = ['image'];
}
