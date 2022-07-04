<?php

namespace App\Models\Records;

use App\Models\Person;
use App\Models\Qualification as QualificationModel;
use App\Traits\HasAuthor;
use App\Traits\HasDocument;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Qualification extends Model
{
	use HasAuthor;
	use HasDocument;
	use HasFactory;

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'records_qualifications';

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function person()
	{
		return $this->belongsTo(Person::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function qualification()
	{
		return $this->belongsTo(QualificationModel::class);
	}
}
