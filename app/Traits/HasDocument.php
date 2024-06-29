<?php

namespace App\Traits;

use App\Models\Document;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Eloquent
 */
trait HasDocument
{
    public static function bootHasDocument(): void
    {
        static::retrieved(fn ($model) => $model->append('document_parsed'));
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
                $resource = clone $this;
                $user = in_array(HasUser::class, class_uses_recursive(get_class($resource)))
                    ? $resource->user
                    : null;

                return optional($this->document, fn (Document $document) => $document->toHtml($user, $resource)) ?? null;
            }
        )->shouldCache();
    }
}
