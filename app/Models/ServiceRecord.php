<?php

namespace App\Models;

use App\Models\Scopes\ServiceRecordScope;
use App\Traits\HasAttachments;
use App\Traits\HasAuthor;
use App\Traits\HasDocument;
use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ServiceRecord
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read \App\Models\User|null $author
 * @property-read \App\Models\Document|null $document
 * @property-read \App\Models\User|null $user
 *
 * @method static \Database\Factories\ServiceRecordFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRecord forAuthor($user)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRecord forDocument($document)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRecord forUser(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRecord query()
 * @mixin \Eloquent
 */
class ServiceRecord extends Model
{
    use HasAttachments;
    use HasAuthor;
    use HasDocument;
    use HasFactory;
    use HasUser;

    /**
     * @var string[]
     */
    protected $fillable = ['user_id', 'document_id', 'author_id', 'text'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'records_service';

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new ServiceRecordScope);
    }
}
