<?php

namespace Perscom\MessageField;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class MessageField extends Field
{
    public $component = 'message-field';

    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);
    }

    public function text(string $text): static
    {
        return $this->withMeta([
            'text' => $text,
        ]);
    }

    public function color(string $color): static
    {
        return $this->withMeta([
            'color' => $color,
        ]);
    }

    //    public function isShownOnDetail(NovaRequest $request, $resource): bool
    //    {
    //        $result = parent::isShownOnDetail($request, $resource);
    //
    //        if ($result) {
    //            $this->withMeta([
    //                'style' => optional($this->meta['style'], function (string $style) {
    //                    return $style . ' ' .
    //                })
    //            ]);
    //        }
    //
    //        return $result;
    //    }
}
