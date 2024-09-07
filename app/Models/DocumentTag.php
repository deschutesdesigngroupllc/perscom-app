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
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentTag document(\App\Models\Document $document)
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentTag whereDocumentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentTag whereTagId($value)
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
