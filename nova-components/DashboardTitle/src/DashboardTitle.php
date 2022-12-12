<?php

namespace Perscom\DashboardTitle;

use Closure;
use Laravel\Nova\Card;

class DashboardTitle extends Card
{
	/**
	 * The width of the card (1/3, 1/2, or full).
	 *
	 * @var string
	 */
	public $width = self::FULL_WIDTH;

	/**
	 * The height strategy of the card.
	 *
	 * @var string
	 */
	public $height = self::DYNAMIC_HEIGHT;

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
    public function withTitle(mixed $title): DashboardTitle
    {
    	if ($title instanceof Closure) {
    		return $this->withMeta([
    			'title' => $title()
		    ]);
	    }

    	return $this->withMeta([
    		'title' => $title
	    ]);
    }

	/**
	 * @param  string  $subtitle
	 *
	 * @return DashboardTitle
	 */
    public function withSubtitle(mixed $subtitle): DashboardTitle
    {
	    if ($subtitle instanceof Closure) {
		    return $this->withMeta([
			    'subtitle' => $subtitle()
		    ]);
	    }

    	return $this->withMeta([
    		'subtitle' => $subtitle
	    ]);
    }
}
