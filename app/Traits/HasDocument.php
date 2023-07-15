<?php

namespace App\Traits;

use App\Models\Document;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasDocument
{
    public function scopeForDocument(Builder $query, Document $document): void
    {
        $query->whereBelongsTo($document);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
