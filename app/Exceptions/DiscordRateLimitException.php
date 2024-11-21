<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Support\HigherOrderTapProxy;

class DiscordRateLimitException extends Exception
{
    protected ?array $data = null;

    public static function withData(array $data): DiscordRateLimitException|HigherOrderTapProxy
    {
        return tap(new DiscordRateLimitException, function (DiscordRateLimitException $exception) use ($data) {
            $exception->setData($data);
        });
    }

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function getData(): ?array
    {
        return $this->data;
    }
}
