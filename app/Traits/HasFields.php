<?php

namespace App\Traits;

use App\Models\Element;
use App\Models\Field;
use Closure;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

trait HasFields
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function fields()
    {
        return $this->morphToMany(Field::class, 'model', 'model_has_fields')
            ->using(Element::class)
            ->as('fields')
            ->withPivot(['order'])
            ->orderBy('order')
            ->withTimestamps();
    }

    /**
     * @return Hidden|Panel|mixed[]
     */
    protected function getNovaFields(NovaRequest $request, bool $wrapInPanel = false, string|Closure $panelName = 'Panel', Closure $modelResolver = null)
    {
        if (($request->isUpdateOrUpdateAttachedRequest() || $request->isPresentationRequest()) &&
            $request->resource() == static::class) { // @phpstan-ignore-line

            $model = $request->findModel();

            if ($modelResolver instanceof Closure) {
                $model = $modelResolver($model);
            }

            $fields = collect(optional($model?->fields, static function ($fields) {
                return $fields->map(static function ($field) {
                    return $field->constructNovaField();
                });
            }));

            if ($wrapInPanel && $fields->isNotEmpty()) {
                return Panel::make(value($panelName, $model), $fields->toArray());
            }

            if ($fields->isNotEmpty()) {
                return $fields->toArray();
            }
        }

        return Hidden::make('No Fields');
    }
}
