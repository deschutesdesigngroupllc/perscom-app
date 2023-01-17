<?php

namespace Perscom\AlertCard;

use Illuminate\Support\Arr;
use Laravel\Nova\Card;

class AlertCard extends Card
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
        return 'alert-card';
    }

    /**
     * @param  array|null  $announcements
     * @return AlertCard
     */
    public function withAnnouncements(array $announcements = null): AlertCard
    {
        $announcements = Arr::isList($announcements) ? $announcements : [$announcements];

        return $this->withMeta([
            'announcements' => $announcements,
        ]);
    }

    /**
     * @param  array|null  $messages
     * @return AlertCard
     */
    public function withSystemMessages(array $messages = null): AlertCard
    {
        $messages = Arr::isList($messages) ? $messages : [$messages];

        return $this->withMeta([
            'messages' => $messages,
        ]);
    }

    /**
     * @param  null  $message
     * @param  null  $button
     * @param  null  $url
     * @return AlertCard
     */
    public function withSubscriptionMessage($message = null, $button = null, $url = null): AlertCard
    {
        return $this->withMeta([
            'subscription' => [
                'message' => $message,
                'button' => $button,
                'url' => $url,
            ],
        ]);
    }
}
