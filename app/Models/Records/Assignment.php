<?php

namespace App\Models\Records;

use App\Models\Position;
use App\Models\Specialty;
use App\Models\Unit;
use App\Models\User;
use App\Traits\HasAttachments;
use App\Traits\HasAuthor;
use App\Traits\HasDocument;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasAttachments;
    use HasAuthor;
    use HasDocument;
    use HasFactory;

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

        static::created(function (Assignment $record) {
            if ($record->user) {
                $record->user->position_id = $record->position?->id;
                $record->user->specialty_id = $record->specialty?->id;
                $record->user->unit_id = $record->unit?->id;
                $record->user->save();
            }
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
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
