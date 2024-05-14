<?php

namespace App\Traits;

use App\Models\Document;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Eloquent
 */
trait HasDocument
{
    public function scopeDocument(Builder $query, Document $document): void
    {
        $query->whereBelongsTo($document);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
