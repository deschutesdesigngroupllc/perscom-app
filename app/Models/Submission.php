<?php

namespace App\Models;

use App\Models\Scopes\SubmissionScope;
use App\Traits\HasStatuses;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Actions\Actionable;
use Stancl\VirtualColumn\VirtualColumn;

class Submission extends Model
{
    use Actionable;
    use HasFactory;
    use HasStatuses;
    use VirtualColumn;

    /**
     * @var array
     */
    public $guarded = [];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['form', 'user', 'statuses'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'form_id'];

    /**
     * @return string[]
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'form_id',
            'user_id',
            'data',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new SubmissionScope);
    }

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
