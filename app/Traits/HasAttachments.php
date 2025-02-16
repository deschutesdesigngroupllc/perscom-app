<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Attachment;
use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @mixin Eloquent
 *
 * @template TModel of Model
 */
trait HasAttachments
{
    /**
     * @return MorphMany<Attachment, TModel>
     */
    public function attachments(): MorphMany
    {
        /** @var TModel $this */
        return $this->morphMany(Attachment::class, 'model');
    }
}
