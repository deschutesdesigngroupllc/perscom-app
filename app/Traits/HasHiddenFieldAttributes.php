<?php

namespace App\Traits;

use App\Models\Field;
use Illuminate\Support\Facades\Gate;

trait HasHiddenFieldAttributes
{
    /**
     * @return array
     */
    public function getAttributesToHide()
    {
        $fields = Field::withoutGlobalScopes()->get();

        if ($fields->isNotEmpty() && ! Gate::check('update', $fields->first())) {
            return $fields->filter->hidden->map->key->toArray();
        }

        return [];
    }

    /**
     * Get the hidden attributes for the model.
     *
     * @return array<string>
     */
    public function getHidden()
    {
        return array_merge(parent::getHidden(), $this->getAttributesToHide());
    }
}
