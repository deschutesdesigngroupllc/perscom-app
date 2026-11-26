<?php

declare(strict_types=1);

namespace App\Data\Feeds;

use Carbon\CarbonInterface;
use Spatie\LaravelData\Data;

class FeedItemData extends Data
{
    public function __construct(
        public string $guid,
        public string $title,
        public string $description,
        public ?string $author,
        public ?CarbonInterface $date,
    ) {}
}
