<?php

declare(strict_types=1);

namespace Tests\Contracts;

use Illuminate\Database\Eloquent\Factories\Factory;

interface ApiResourceTestContract
{
    public function endpoint(): string;

    public function model(): string;

    public function controller(): string;

    public function factory(): Factory;

    /**
     * @return string[]
     */
    public function scopes(): array;

    /**
     * @return string[]
     */
    public function storeData(): array;

    /**
     * @return string[]
     */
    public function updateData(): array;
}
