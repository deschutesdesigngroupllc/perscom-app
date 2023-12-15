<?php

namespace Perscom\MessageField;

use Laravel\Nova\Fields\Field;

class MessageField extends Field
{
    public $component = 'message-field';

    public function __construct($name, $attribute = null, ?callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->fillUsing(fn () => null);
        $this->nullable();
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
}
