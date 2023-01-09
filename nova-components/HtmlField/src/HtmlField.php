<?php

namespace Perscom\HtmlField;

use Laravel\Nova\Fields\Field;

class HtmlField extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'HtmlField';

    /**
     * @param $html
     * @return HtmlField
     */
    public function html($html)
    {
        if ($html instanceof \Closure || (\is_callable($html) && \is_object($html))) {
            $html = $html();
        }

        return $this->withMeta([
            'html' => $html,
        ]);
    }

    /**
     * @param  string  $view
     * @return HtmlField
     */
    public function view(string $view)
    {
        return $this->html(view($view)->render());
    }
}
