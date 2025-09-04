<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasDocument;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $tag_id
 * @property int $document_id
 * @property-read Document $document
 * @property-read mixed $document_parsed
 *
 * @method static Builder<static>|DocumentTag document(Document $document)
 * @method static Builder<static>|DocumentTag newModelQuery()
 * @method static Builder<static>|DocumentTag newQuery()
 * @method static Builder<static>|DocumentTag query()
 * @method static Builder<static>|DocumentTag whereDocumentId($value)
 * @method static Builder<static>|DocumentTag whereTagId($value)
 *
 * @mixin \Eloquent
 */
class DocumentTag extends Pivot
{
    use HasDocument;

    protected $table = 'documents_tags';

    protected $fillable = [
        'tag_id',
    ];
}
