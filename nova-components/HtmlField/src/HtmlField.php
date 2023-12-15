<?php

namespace Perscom\HtmlField;

use Laravel\Nova\Fields\Field;

class HtmlField extends Field
{
    public $component = 'HtmlField';

    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->fillUsing(fn () => null);
        $this->nullable();
    }

    public function html($html): HtmlField
    {
        if ($html instanceof \Closure || (\is_callable($html) && \is_object($html))) {
            $html = $html();
        }

        return $this->withMeta([
            'html' => $html,
        ]);
    }

    public function view(string $view, $data = [], $mergeData = []): HtmlField
    {
        return $this->html(view($view, $data, $mergeData)->render());
    }
}
