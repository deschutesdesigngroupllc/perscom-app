<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Attachment;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @mixin Eloquent
 */
trait HasAttachments
{
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'model');
    }
}
