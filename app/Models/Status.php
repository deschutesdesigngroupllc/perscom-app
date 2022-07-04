<?php

namespace App\Models;

use App\Models\Forms\Submission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
	 */
    public function people()
    {
    	return $this->morphedByMany(Person::class, 'model', 'model_has_statuses');
    }

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
	 */
	public function submissions()
	{
		return $this->morphedByMany(Submission::class, 'model', 'model_has_statuses');
	}
}
