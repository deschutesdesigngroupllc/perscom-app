<?php

namespace App\Traits;

use App\Models\Document;

trait HasDocument
{
    /**
     * @param  Builder  $query
     * @param  Document  $user
     * @return Builder
     */
    public function scopeForDocument($query, $document)
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
