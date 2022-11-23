<?php

namespace Perscom\DashboardTitle;

use Laravel\Nova\Card;

class DashboardTitle extends Card
{
    /**
     * The width of the card (1/3, 1/2, or full).
     *
     * @var string
     */
    public $width = '1/3';

    /**
     * Get the component name for the element.
     *
     * @return string
     */
    public function component()
    {
        return 'dashboard-title';
    }

	/**
	 * @param  string  $title
	 *
	 * @return DashboardTitle
	 */
    public function withTitle(string $title): DashboardTitle
    {
    	return $this->withMeta([
    		'title' => $title
	    ]);
    }

	/**
	 * @param  string  $subtitle
	 *
	 * @return DashboardTitle
	 */
    public function withSubtitle(string $subtitle): DashboardTitle
    {
    	return $this->withMeta([
    		'subtitle' => $subtitle
	    ]);
    }
}
