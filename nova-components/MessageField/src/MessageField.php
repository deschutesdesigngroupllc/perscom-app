<?php

namespace Perscom\MessageField;

use Laravel\Nova\Fields\Field;

class MessageField extends Field
{
    public $component = 'message-field';

    public function color(string $color): static
    {
        return $this->withMeta([
            'color' => $color,
        ]);
    }
}
