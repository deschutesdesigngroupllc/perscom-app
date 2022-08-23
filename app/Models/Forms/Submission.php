<?php

namespace App\Models\Forms;

use App\Casts\SubmissionCast;
use App\Models\Field;
use App\Models\User;
use App\Traits\HasStatuses;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Nova\Actions\Actionable;

class Submission extends Model
{
    use Actionable;
    use HasFactory;
    use HasStatuses;

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['form', 'user'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'form_id', 'data'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'json',
    ];

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
