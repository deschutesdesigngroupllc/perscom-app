<?php

namespace App\Traits;

use App\Models\Field;
use Eloquent;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

/**
 * @mixin Eloquent
 */
trait HasHiddenFieldAttributes
{
    protected static ?Collection $fields = null;

    /**
     * @return array<int, mixed>
     */
    public function getAttributesToHide(): array
    {
        if (is_null(self::$fields)) {
            self::$fields = Field::withoutGlobalScopes()->get();

            if (self::$fields->isNotEmpty() && ! Gate::check('update', self::$fields->first())) {
                return self::$fields->filter->hidden->map->key->toArray();
            }
        }

        return [];
    }

    public function getHidden(): array
    {
        return array_merge(parent::getHidden(), $this->getAttributesToHide());
    }
}
