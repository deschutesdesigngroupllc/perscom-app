<?php

namespace Perscom\Newsfeed;

use Laravel\Nova\Card;

class Newsfeed extends Card
{
    /**
     * The width of the card (1/3, 1/2, or full).
     *
     * @var string
     */
    public $width = '2/3';

    /**
     * Get the component name for the element.
     *
     * @return string
     */
    public function component()
    {
        return 'newsfeed';
    }
}
