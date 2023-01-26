<?php

namespace App\Models;

use App\Models\Scopes\AssignmentRecordScope;
use App\Traits\HasAttachments;
use App\Traits\HasAuthor;
use App\Traits\HasDocument;
use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentRecord extends Model
{
    use HasAttachments;
    use HasAuthor;
    use HasDocument;
    use HasFactory;
    use HasUser;

    /**
     * @var string[]
     */
    protected $fillable = ['user_id', 'unit_id', 'position_id', 'specialty_id', 'document_id', 'author_id', 'text'];

    /**
     * @var string[]
     */
    protected $with = ['position', 'specialty', 'unit'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'records_assignments';

    /**
     * Boot
     */
    public static function boot()
    {
        parent::boot();

        static::created(function (AssignmentRecord $record) {
            if ($record->user) {
                $record->user->position_id = $record->position?->id;
                $record->user->specialty_id = $record->specialty?->id;
                $record->user->unit_id = $record->unit?->id;
                $record->user->save();
            }
        });
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new AssignmentRecordScope);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
