<?php

namespace App\Nova\Fields;

use App\Nova\Field;
use Codinglabs\FeatureFlags\Enums\FeatureState as Enum;
use Laravel\Nova\Fields\Select;
use Illuminate\Support\Str;

class FeatureState extends Select
{
    public function __construct($name, $attribute = null, $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->resolveUsing(function ($value) {
            return $value instanceof Enum ? $value->value : $value;
        });

        $this->displayUsing(function ($value) {
            return $value instanceof Enum
                ? Str::ucfirst($value->value)
                : $value;
        });

        $this->fillUsing(function ($request, $model, $attribute, $requestAttribute) {
            if ($request->exists($requestAttribute)) {
                $model->{$attribute} = Enum::from($request[$requestAttribute]);
            }
        });
    }

    public function attach($class)
    {
        return $this->options($class::toArray())->rules('required');
    }
}
