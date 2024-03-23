<?php

namespace App\Traits;

use App\Models\Document;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Eloquent
 */
trait HasDocument
{
    public function initializeHasDocument(): void
    {
        if (! data_get($this->appends, 'document_parsed')) {
            $this->appends[] = 'document_parsed';
        }
    }

    public function scopeDocument(Builder $query, Document $document): void
    {
        $query->whereBelongsTo($document);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function documentParsed(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! is_null($this->document_id)) {
                    $class = get_class($this);

                    return $this->document->toHtml(User::find($this->user_id), call_user_func([$class, 'find'], $this->id));
                }

                return null;
            }
        );
    }
}
