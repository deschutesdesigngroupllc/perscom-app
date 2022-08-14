<?php

namespace App\Traits;

use App\Models\Attachment;

trait HasAttachments
{
    /**
     * @return mixed
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'model');
    }
}
