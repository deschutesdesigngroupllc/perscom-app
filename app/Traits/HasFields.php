<?php

namespace App\Traits;

use App\Models\Element;
use App\Models\Field;
use Closure;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

trait HasFields
{
    public function fields(): MorphToMany
    {
        return $this->morphToMany(Field::class, 'model', 'model_has_fields')
            ->using(Element::class)
            ->as('fields')
            ->withPivot(['order'])
            ->orderBy('order')
            ->withTimestamps();
    }

    /**
     * @return array<int, mixed>|Panel|Hidden
     */
    protected function getNovaFields(NovaRequest $request, bool $wrapInPanel = false, string|Closure $panelName = 'Panel', ?Closure $modelResolver = null): array|Panel|Hidden
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
