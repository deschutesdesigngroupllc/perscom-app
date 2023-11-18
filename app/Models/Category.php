<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['name', 'description', 'resource'];

    public function awards(): BelongsToMany
    {
        return $this->belongsToMany(Award::class, 'awards_categories')
            ->withPivot('order')
            ->ordered()
            ->withTimestamps();
    }
}
