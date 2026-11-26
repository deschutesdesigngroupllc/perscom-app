<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasDocument;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $tag_id
 * @property int $document_id
 * @property-read Document $document
 * @property-read string|null $document_parsed
 *
 * @method static Builder<static>|DocumentTag document(\App\Models\Document $document)
 * @method static Builder<static>|DocumentTag newModelQuery()
 * @method static Builder<static>|DocumentTag newQuery()
 * @method static Builder<static>|DocumentTag query()
 * @method static Builder<static>|DocumentTag whereDocumentId($value)
 * @method static Builder<static>|DocumentTag whereTagId($value)
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
#[Fillable([
    'tag_id',
])]
#[Table(name: 'documents_tags')]
class DocumentTag extends Pivot
{
    use HasDocument;
}
