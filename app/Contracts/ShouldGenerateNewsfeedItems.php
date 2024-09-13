<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\User;

interface ShouldGenerateNewsfeedItems
{
    public function headlineForNewsfeedItem(): string;

    public function textForNewsfeedItem(): string;

    public function itemForNewsfeedItem(): ?string;

    public function recipientForNewsfeedItem(): ?User;
}
