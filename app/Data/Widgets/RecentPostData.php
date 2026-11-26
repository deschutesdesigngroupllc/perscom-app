<?php

declare(strict_types=1);

namespace App\Data\Widgets;

use App\Models\User;
use Carbon\CarbonInterface;
use Spatie\LaravelData\Data;

class RecentPostData extends Data
{
    public function __construct(
        public mixed $key,
        public string $title,
        public string $excerpt,
        public ?string $color,
        public ?User $author,
        public ?CarbonInterface $date,
        public ?string $note,
    ) {}
}
