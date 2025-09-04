<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasDocument;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $document_id
 * @property int $category_id
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Document $document
 * @property-read mixed $document_parsed
 *
 * @method static Builder<static>|DocumentCategory document(\App\Models\Document $document)
 * @method static Builder<static>|DocumentCategory newModelQuery()
 * @method static Builder<static>|DocumentCategory newQuery()
 * @method static Builder<static>|DocumentCategory query()
 * @method static Builder<static>|DocumentCategory whereCategoryId($value)
 * @method static Builder<static>|DocumentCategory whereCreatedAt($value)
 * @method static Builder<static>|DocumentCategory whereDocumentId($value)
 * @method static Builder<static>|DocumentCategory whereId($value)
 * @method static Builder<static>|DocumentCategory whereOrder($value)
 * @method static Builder<static>|DocumentCategory whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class DocumentCategory extends Pivot
{
    use HasDocument;

    protected $table = 'documents_categories';

    protected $fillable = [
        'category_id',
        'order',
        'created_at',
        'updated_at',
    ];
}
