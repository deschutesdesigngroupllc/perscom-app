<?php

namespace Perscom\AlertCard;

use Illuminate\Support\Arr;
use Laravel\Nova\Card;

class AlertCard extends Card
{
    /**
     * @var string
     */
    public $width = self::FULL_WIDTH;

    /**
     * @var string
     */
    public $height = self::DYNAMIC_HEIGHT;

    public function component(): string
    {
        return 'alert-card';
    }

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

    public function withSubscriptionMessage(?string $message = null, ?string $button = null, ?string $url = null): AlertCard
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
