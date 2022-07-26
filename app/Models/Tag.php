<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Tags\Tag as TagModel;

class Tag extends TagModel
{
    use HasFactory;

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
    public function taggables()
    {
    	return $this->hasMany(Taggable::class);
    }
}
