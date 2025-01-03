<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasDocument;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $tag_id
 * @property int $document_id
 * @property-read Document $document
 * @property-read mixed $document_parsed
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentTag document(\App\Models\Document $document)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentTag query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentTag whereDocumentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentTag whereTagId($value)
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
