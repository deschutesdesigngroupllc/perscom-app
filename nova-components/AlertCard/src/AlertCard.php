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
     * @param  null  $title
     * @param  null  $content
     * @param  null  $color
     * @return AlertCard
     */
    public function withAnnouncement($title = null, $content = null, $color = null): AlertCard
    {
        $meta = $this->meta();
        $existingMessages = Arr::get($meta, 'announcements', []);
        $newMessages = Arr::prepend($existingMessages, [
            'title' => $title,
            'content' => $content,
            'color' => $color,
        ]);
        $newMeta = Arr::set($meta, 'announcements', $newMessages);

        return $this->withMeta($newMeta);
    }

    /**
     * @param  null  $title
     * @param  null  $message
     * @param  null  $button
     * @param  null  $url
     * @return AlertCard
     */
    public function withSystemMessage($title = null, $message = null, $button = null, $url = null): AlertCard
    {
        $meta = $this->meta();
        $existingMessages = Arr::get($meta, 'messages', []);
        $newMessages = Arr::prepend($existingMessages, [
            'title' => $title,
            'message' => $message,
            'button' => $button,
            'url' => $url,
        ]);
        $newMeta = Arr::set($meta, 'messages', $newMessages);

        return $this->withMeta($newMeta);
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
