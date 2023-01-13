<?php

namespace App\Models\Records;

use App\Models\User;
use App\Traits\HasAttachments;
use App\Traits\HasAuthor;
use App\Traits\HasDocument;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasAttachments;
    use HasAuthor;
    use HasDocument;
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'document_id',
        'author_id',
        'text',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'records_service';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
