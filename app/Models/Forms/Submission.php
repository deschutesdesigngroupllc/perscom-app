<?php

namespace App\Models\Forms;

use App\Models\User;
use App\Traits\HasStatuses;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;
    use HasStatuses;

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['form', 'user'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}
