<?php

namespace App\Traits;

use App\Models\Document;
use Illuminate\Database\Eloquent\Builder;

trait HasDocument
{
    /**
     * @param  Builder  $query
     * @param  Document  $document
     * @return Builder
     */
    public function scopeDocument($query, $document)
    {
        return $query->whereBelongsTo($document);
    }

    /**
     * @return mixed
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
