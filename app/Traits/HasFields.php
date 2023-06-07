<?php

namespace App\Traits;

use Closure;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

trait HasFields
{
    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    private function resolveModelForFields(NovaRequest $request, $modelResolver = null)
    {
        if (! ($request->isUpdateOrUpdateAttachedRequest() || $request->isPresentationRequest()) ||
            $request->resource() !== \get_called_class()) {
            return null;
        }

        $model = $request->findModel();

        if ($modelResolver instanceof Closure) {
            return $modelResolver($model);
        }

        return $model;
    }

    /**
     * @return array|Panel|mixed[]
     */
    protected function getFields(NovaRequest $request, bool $wrapInPanel = false, string|Closure $panelName = 'Panel', Closure $modelResolver = null)
    {
        $model = $this->resolveModelForFields($request, $modelResolver);

        $fields = collect(optional($model?->fields, static function ($fields) {
            return $fields->map(static function ($field) {
                return $field->constructNovaField();
            });
        }));

        if ($fields->isEmpty()) {
            return Hidden::make('No Fields');
        }

        if ($wrapInPanel && $fields->isNotEmpty()) {
            if ($panelName instanceof Closure) {
                $panelName = $panelName($model);
            }

            return Panel::make($panelName ?? 'Fields', $fields->toArray());
        }

        return $fields->toArray();
    }
}
