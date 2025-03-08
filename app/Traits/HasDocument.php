<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Document;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Eloquent
 *
 * @template TModel of Model
 */
trait HasDocument
{
    public function scopeDocument(Builder $query, Document $document): void
    {
        $query->whereBelongsTo($document);
    }

    /**
     * @return BelongsTo<Document, TModel>
     */
    public function document(): BelongsTo
    {
        /** @var TModel $this */
        return $this->belongsTo(Document::class);
    }

    public function documentParsed(): Attribute
    {
        return Attribute::make(
            get: function () {
                $resource = clone $this;
                $user = in_array(HasUser::class, class_uses_recursive($resource::class)) && method_exists($resource, 'user')
                    ? $resource->user // @phpstan-ignore-line
                    : null;

                return optional($this->document, fn (Document $document): string => $document->toHtml($user, $resource));
            }
        )->shouldCache();
    }

    protected function initializeHasDocument(): void
    {
        $this->append('document_parsed');
        $this->mergeFillable(['document_id']);
    }
}
