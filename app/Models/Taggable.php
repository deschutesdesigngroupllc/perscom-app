<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Taggable extends Model
{
    use HasFactory;

	/**
	 * @var string
	 */
    protected $table = 'taggables';

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\MorphTo
	 */
    public function taggable()
    {
    	return $this->morphTo('taggable');
    }
}
